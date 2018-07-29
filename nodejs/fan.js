const stream = require('./helpers/streamBlock')
const call = require('./helpers/nodeCall')
const config = require('./config')
const fetch = require('node-fetch')
const con = require('./mysql')
// const XMLHttpRequest = require('xmlhttprequest').XMLHttpRequest
const wifkey = config.wifkey

let fans = []

// we will store fans in the array
// to check authors against this array by indexOf
con.query('SELECT `fan` FROM `fans` WHERE `followers`>0')
  .then(results => {
    for (let i in results) {
      fans.push(results[i].fan)
    }
  }).catch(e => {
    throw new Error(e)
  })

// Updating Users List Every 10 Minutes
setInterval(() => {
  try {
    con.query('SELECT `fan` FROM `fans` WHERE `followers`>0')
      .then(results => {
        var nusers = []
        for (let i in results) {
          nusers.push(results[i].fan)
        }
        fans = nusers
      })
  } catch (e) {
    throw new Error(e)
  }
}, 600000)

// we will use this regext to detect and skip edited posts
const editRegex = /^(@@+.+@@)/

// Streaming blocks with custom methods
const startstream = async () => {
  try {
    stream.streamBlockOperations(async ops => {
      if (ops) {
        const op = ops[0]
        // we will skip comments (comments have parent_author)
        // we will detect only new posts
        if (op[0] === 'comment' && op[1].parent_author === '') {
          if (fans.indexOf(op[1].author) > -1) {
            if (!(op[1].body).match(editRegex)) {
              fanupvote(op[1].author, op[1].permlink)
            }
          }
        }
      }
    })
  } catch (e) {
    throw new Error(e)
  }
}
startstream()

// process upvotes for the Fanbase followers
const fanupvote = async (author, permlink) => {
  try {
    const results = await con.query(
      'SELECT `follower`,`weight`,`aftermin` FROM `fanbase` WHERE `fan` =? AND `enable`=1 AND `limitleft`>0',
      [author]
    )
    for (let i in results) {
      const follower = results[i].follower
      const weight = results[i].weight
      const aftermin = results[i].aftermin
      const nowdate = new Date()
      const nowsec = nowdate.getTime() / 1000
      const now = Math.floor(nowsec)
      if (aftermin > 0) {
        // user requested to upvote post after a delay
        // we will insert information to the database to upvote later (delay.js)
        const time = parseInt(now + (aftermin * 60))
        con.query(
          'INSERT INTO `upvotelater`(`voter`, `author`, `permlink`, `weight`, `time`, `trail_fan`) VALUES (?,?,?,?,?,"2")',
          [follower, author, permlink, weight, time]
        )
        // update fanbase daily upvote limitaion in the database
        con.query(
          'UPDATE `fanbase` SET `limitleft`=`limitleft`-1 WHERE `fan`=? AND `follower`=?',
          [author, follower]
        )
      } else {
        // we should process upvote right now
        // first we will check limitations
        checkpowerlimit(follower, author, permlink, weight)
        // update fanbase daily limitation in the database
        con.query(
          'UPDATE `fanbase` SET `limitleft`=`limitleft`-1 WHERE `fan`=? AND `follower`=?',
          [author, follower]
        )
      }
    }
  } catch (e) {
    throw new Error(e)
  }
}

let tvfs
let tvs
// Check voting power limit
const updateGlobals = async () => {
  try {
    // get dynamic global propertise for just appbase! (v0.19.10)
    // will need change in other version nodes
    const result = await call(
      config.steemd,
      'condenser_api.get_dynamic_global_properties',
      []
    )
    // on any error, result will be null
    if (!result) return 1
    tvfs = parseInt(result.total_vesting_fund_steem.replace('STEEM', ''))
    tvs = parseInt(result.total_vesting_shares.replace('VESTS', ''))
  } catch (e) {
    throw new Error(e)
  }
}
updateGlobals()
setInterval(() => {
  updateGlobals()
}, 900000)

// we will check limitations then broadcast upvote
const checkpowerlimit = async (voter, author, permlink, weight) => {
  try {
    const results = await con.query(
      'SELECT `limit_power` FROM `users` WHERE `user`=?',
      [voter]
    )
    const powerlimit = results[0].limit_power
    // Get accounts information from appbase (v0.19.10)
    // will need change in other versions
    const result = await call(
      config.steemd,
      'condenser_api.get_accounts',
      [
        [voter]
      ]
    )
    // on any error, result will be null
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
      // calculating total SP to check against limitation
      const delegated = parseInt(u.delegated_vesting_shares.replace('VESTS', '')) // VESTS
      const received = parseInt(u.received_vesting_shares.replace('VESTS', '')) // VESTS
      const vesting = parseInt(u.vesting_shares.replace('VESTS', '')) // VESTS
      const totalvest = vesting + received - delegated
      let sp = totalvest * (tvfs / tvs)
      sp = sp.toFixed(2)
      if (powernow > powerlimit) {
        if (((powernow / 100) * (weight / 10000) * sp) > 1.5) {
          // Don't broadcast upvote if sp*weight*power < 1.5
          upvote(voter, author, permlink, weight)
        }
      }
    }
  } catch (e) {
    throw new Error(e)
  }
}

// this function will send post and voter information to another app to upvote
const upvote = async (voter, author, permlink, weight) => {
  try {
    // Upvote server url for handling upvotes
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

console.log('Fan Started.')
