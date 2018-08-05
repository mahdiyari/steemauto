const stream = require('./helpers/streamBlock')
const call = require('./helpers/nodeCall')
const upvote = require('./helpers/broadcastUpvote')
const checkLimits = require('./helpers/checkLimits')
const config = require('./config')
const con = require('./mysql')
const isSteemd = config.isSteemd
// we are using this variable to change methods according to the node version

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
      isSteemd ? config.steemd : config.rpc,
      isSteemd ? 'condenser_api.get_content' : 'get_content',
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
            upvoteLater(follower, author, permlink, weight, time, userr)
          } else {
            // User configured to vote just now
            // check limitations then broadcast upvote
            const result = await checkLimits(follower, author, permlink, weight)
            // broadcast upvote if user detail is not limited
            if (result) upvote(follower, author, permlink, weight)
          }
        }
      }
    }
  } catch (e) {
    throw new Error(e)
  }
}
const upvoteLater = async (follower, author, permlink, weight, time, userr) => {
  await con.query(
    'INSERT INTO `upvotelater`' +
    '(`voter`, `author`, `permlink`, `weight`, `time`,`trail_fan`,`trailer`)' +
    'VALUES (?,?,?,?,?,"0",?)',
    [follower, author, permlink, weight, time, userr]
  )
}

console.log('Trail Started.')
