const stream = require('./helpers/streamBlock')
const upvote = require('./helpers/broadcastUpvote')
const checkLimits = require('./helpers/checkLimits')
const con = require('./mysql')

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

// Pool of indexes
// with help of this indexex, we will store recent posts to a variable
// to prevent multiple in a row upvotes
let index = 0
let maxIndex = 500
const authorIndex = () => {
  if (index < maxIndex) index += 1
  else index = 0
  return index
}
const recentAuthors = []

// this function will return true if the author's recent post was older than 4 minutes
// this will help us to prevent abusers from submitting edit posts in a row
const checkAuthor = (user) => {
  const nowdate = new Date()
  const nowsec = nowdate.getTime() / 1000
  const now = Math.floor(nowsec)
  const authors = recentAuthors.map(data => data.author)
  const index = authors.indexOf(user)
  if (index > -1) {
    if (now - recentAuthors[index].date > 240) {
      recentAuthors[authorIndex()] = {
        author: user,
        date: now
      }
      return true
    } else {
      return false
    }
  } else {
    recentAuthors[authorIndex()] = {
      author: user,
      date: now
    }
    return true
  }
}

// process upvotes for the Fanbase followers
const fanupvote = async (author, permlink) => {
  try {
    // check author's recent post date
    if (checkAuthor(author)) {
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
          upvoteLater(follower, author, permlink, weight, time)
          // update fanbase daily upvote limitaion in the database
          updateDailyLimit(author, follower)
        } else {
          // we should process upvote right now
          // first we will check limitations
          const result = await checkLimits(follower, author, permlink, weight)
          // broadcast upvote if user detail is not limited
          if (result) upvote(follower, author, permlink, weight)
          // update fanbase daily limitation in the database
          updateDailyLimit(author, follower)
        }
      }
    }
  } catch (e) {
    throw new Error(e)
  }
}

const updateDailyLimit = async (author, follower) => {
  try {
    await con.query(
      'UPDATE `fanbase` SET `limitleft`=`limitleft`-1 WHERE `fan`=? AND `follower`=?',
      [author, follower]
    )
  } catch (e) {
    throw new Error(e)
  }
}

const upvoteLater = async (follower, author, permlink, weight, time) => {
  try {
    con.query(
      'INSERT INTO `upvotelater`(`voter`, `author`, `permlink`, `weight`, `time`, `trail_fan`) VALUES (?,?,?,?,?,"2")',
      [follower, author, permlink, weight, time]
    )
  } catch (e) {
    throw new Error(e)
  }
}

console.log('Fan Started.')
