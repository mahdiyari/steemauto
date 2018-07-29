const stream = require('./helpers/streamBlock')
const call = require('./helpers/nodeCall')
const config = require('./config')
const fetch = require('node-fetch')
const con = require('./mysql')

const wifkey = config.wifkey

let trails = []

con.query('SELECT `user` FROM `trailers` WHERE `followers`>0')
  .then(results => {
    for (let i in results) {
      trails.push(results[i].user)
    }
  })
  .catch(e => {
    throw new Error(e)
  })

// Updating Trails List Every 10 Minutes
setInterval(() => {
  con.query('SELECT `user` FROM `trailers` WHERE `followers`>0')
    .then(results => {
      let busers = []
      for (let i in results) {
        busers.push(results[i].user)
      }
      trails = busers
    })
    .catch(e => {
      throw new Error(e)
    })
}, 600000)

// We will use this regex to detect and skip comments
const regex = /^re-/

// Streaming Blocks to detect trail upvotes on the posts
// using custom streaming function
const startstream = async () => {
  stream.streamBlockOperations((ops) => {
    const op = ops[0]
    if (op) {
      // we will process just posts and just upvotes (not downvotes)
      if (op[0] === 'vote' && !op[1].permlink.match(regex) && op[1].weight > 0) {
        // this if will skip trailer's posts
        // we want to upvote any posts upvoted by @steemauto (include self posts)
        if (op[1].voter !== op[1].author || op[1].voter === 'steemauto') {
          // check voter against our trails list which is updated from database
          if (trails.indexOf(op[1].voter) > -1) {
            trailupvote(op[1].voter, op[1].author, op[1].permlink, op[1].weight)
          }
        }
      }
    }
  })
}
startstream()

// Process upvotes for the Curation Trail followers
const trailupvote = async (userr, author, permlink, fweight) => {
  try {
    // get_content which works with just appbase (v0.19.10)
    const content = await call(
      config.steemd,
      'condenser_api.get_content',
      [author, permlink]
    )
    // on any possible error, call method will return null
    if (!content) return 1
    const created = new Date(content.created + 'Z')
    const createdtime = created.getTime() / 1000
    const now = new Date()
    const nowtime = now.getTime() / 1000
    const nowseconds = Math.floor(nowtime)
    // we will skip posts which are older than 6.5 days
    if (nowtime - createdtime < 561600) {
      // get list of all users who are following this trail and are enabled
      const results = await con.query(
        'SELECT `follower`,`weight`,`aftermin`,`votingway` FROM `followers`' +
        'WHERE `trailer` =? AND `enable`="1"',
        [userr]
      )
      for (let i in results) {
        const follower = results[i].follower
        // we will skip posts which are already upvoted by same voter (follower)
        let voted = 0
        for (let j in content['active_votes']) {
          if (content['active_votes'][j].voter === follower) {
            voted = 1
            break
          }
        }
        if (voted === 0) {
          let weight = results[i].weight
          const aftermin = results[i].aftermin
          const votingway = results[i].votingway
          // change weight based on trail vote weight for followers who selected 'scale' method
          if (votingway === 1) {
            weight = parseInt((weight / 10000) * fweight)
          }
          if (aftermin > 0) {
            // User configured to upvote after X minutes
            // we will add information to the database
            // to be upvoted later on the time by 'delay.js'
            let time = parseInt(nowseconds + (aftermin * 60))
            time = Math.floor(time)
            await con.query(
              'INSERT INTO `upvotelater`' +
              '(`voter`, `author`, `permlink`, `weight`, `time`,`trail_fan`,`trailer`)' +
              'VALUES (?,?,?,?,?,"0",?)',
              [follower, author, permlink, weight, time, userr]
            )
          } else {
            // User configured to vote just now
            // check limitations then broadcast upvote
            checkpowerlimit(follower, author, permlink, weight, votingway, userr)
          }
        }
      }
    }
  } catch (e) {
    throw new Error(e)
  }
}

let tvfs
let tvs
const updateGlobals = async () => {
  try {
    // get dynamic global propertise for calculating total SP
    // this works just with steemd (v0.19.10) not jussi
    const result = await call(
      config.steemd,
      'condenser_api.get_dynamic_global_properties',
      []
    )
    // on any possible error, call method will return null
    if (!result) return 1
    tvfs = parseInt(result.total_vesting_fund_steem.replace('STEEM', ''))
    tvs = parseInt(result.total_vesting_shares.replace('VESTS', ''))
  } catch (e) {
    throw new Error(e)
  }
}
updateGlobals()

// update global props every 15 minutes
setInterval(() => {
  updateGlobals()
}, 900000)

// Check voting power limit and SP
// then broadcasting upvote
const checkpowerlimit = async (voter, author, permlink, weight, votingway, trailer) => {
  try {
    const results = await con.query(
      'SELECT `limit_power` FROM `users` WHERE `user`=?',
      [voter]
    )
    const powerlimit = results[0].limit_power
    // get_accounts which work with just appbase (v0.19.10)
    const result = await call(
      config.steemd,
      'condenser_api.get_accounts',
      [
        [voter]
      ]
    )
    // on any possible error, call method will return null
    if (!result) return 1
    if (tvfs && tvs) {
      // calculating voting power to check against limitation
      const u = result[0]
      const now = new Date()
      const n = now.getTime() / 1000
      const last = new Date(u.last_vote_time + 'z')
      const l = last.getTime() / 1000
      const power = u.voting_power / 100 + (parseFloat(n - l) / 4320)
      let powernow = power.toFixed(2)
      if (powernow > 100) powernow = 100
      // calculating total account SP to check against limitation
      const delegated = parseInt(u.delegated_vesting_shares.replace('VESTS', '')) // VESTS
      const received = parseInt(u.received_vesting_shares.replace('VESTS', '')) // VESTS
      const vesting = parseInt(u.vesting_shares.replace('VESTS', '')) // VESTS
      const totalvest = vesting + received - delegated
      let sp = totalvest * (tvfs / tvs)
      sp = sp.toFixed(2)
      if (powernow > powerlimit) {
        // Don't broadcast upvote if effective upvote SP < 1.5
        // This avoids low weight errors from rpc node and reduces calls
        if (((powernow / 100) * (weight / 10000) * sp) > 1.5) {
          upvote(voter, author, permlink, weight, votingway, trailer)
        }
      }
    }
  } catch (e) {
    throw new Error(e)
  }
}

// Upvote function for sending upvotes to another app to handle
const upvote = async (voter, author, permlink, weight, votingway, trailer) => {
  try {
    // Upvoting server url for handling upvotes
    const url = config.nodejssrv + ':7412/' +
      '?wif=' + wifkey +
      '&voter=' + voter +
      '&author=' + author +
      '&permlink=' + permlink +
      '&weight=' + weight
    await fetch(url)
  } catch (e) {
    throw new Error(e)
  }
}

console.log('Trail Started.')
