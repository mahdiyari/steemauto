const stream = require('./helpers/streamBlock')
const con = require('./mysql')
const upvote = require('./helpers/broadcastUpvote')
const checkLimit = require('./helpers/checkLimits')

let cmUsers = []

con.query(
  'SELECT DISTINCT `user` FROM `commentupvote`'
).then(results => {
  for (let i in results) {
    cmUsers.push(results[i].user)
  }
}).catch(e => {
  throw new Error(e)
})

// Updating Users List Every 10 Minutes
setInterval(async () => {
  try {
    const results = await con.query(
      'SELECT DISTINCT `user` FROM `commentupvote`'
    )
    let dusers = []
    for (let i in results) {
      dusers.push(results[i].user)
    }
    cmUsers = dusers
  } catch (e) {
    throw new Error(e)
  }
}, 300000)

// Streaming Blocks
const startstream = async () => {
  try {
    stream.streamBlockOperations(async ops => {
      if (ops) {
        const op = ops[0]
        if (op[0] === 'comment' && op[1].parent_author !== '') {
          if (cmUsers.indexOf(op[1].parent_author) > -1) {
            commentupvote(op[1].parent_author, op[1].author, op[1].permlink, op[1].parent_permlink)
          }
        }
      }
    })
  } catch (e) {
    throw new Error(e)
  }
}
startstream()

// Upvoting Comments Automatically //
const commentupvote = async (userr, commenter, permlink, parentpermlink) => {
  try {
    const results = await con.query(
      'SELECT EXISTS(SELECT * FROM `commentupvote`' +
      'WHERE `user`=? AND `commenter`=? AND `enable`="1" AND `todayvote`<2)',
      [userr, commenter]
    )
    for (let i in results[0]) {
      if (results[0][i] === 1) {
        const results = await con.query(
          'SELECT EXISTS(SELECT * FROM `upvotedcomments` WHERE `user`=? AND `permlink`=?)',
          [userr, commenter]
        )
        for (let i in results[0]) {
          if (results[0][i] === 0) {
            const results = await con.query(
              'SELECT `weight`,`aftermin` FROM `commentupvote`' +
              'WHERE `user`=? AND `commenter`=? AND `enable`="1" AND `todayvote`<2',
              [userr, commenter]
            )
            for (let k in results) {
              const weight = results[k].weight
              const aftermin = results[k].aftermin
              const nowdate = new Date()
              const nowsec = nowdate.getTime() / 1000
              const now = Math.floor(nowsec)
              if (aftermin > 0) {
                const time = parseInt(now + (aftermin * 60))
                // insert in to database to upvote later
                await con.query(
                  'INSERT INTO `upvotelater`(`voter`, `author`, `permlink`, `weight`, `time`,`trail_fan`)' +
                  'VALUES (?,?,?,?,?,"3")',
                  [userr, commenter, permlink, weight, time]
                )
                updateDatabase(userr, commenter, parentpermlink, now)
              } else {
                const result = await checkLimit(userr, commenter, permlink, weight)
                // we will broadcast upvote after checking user limitations
                if (result) {
                  upvote(userr, commenter, permlink, weight)
                  updateDatabase(userr, commenter, parentpermlink, now)
                }
              }
            }
          }
        }
      }
    }
  } catch (e) {
    throw new Error(e)
  }
}

// this function will store upvoted comments in the database
// I don't what was the problem without storing these data
// I will try to find reason of this function
const updateDatabase = async (userr, commenter, parentpermlink, now) => {
  await con.query(
    'UPDATE `commentupvote` SET `todayvote`=`todayvote`+1 WHERE `user`=? AND `commenter`=?',
    [userr, commenter]
  )
  await con.query(
    'INSERT INTO `upvotedcomments`(`user`, `permlink`,`time`) VALUES (?,?,?)',
    [commenter, parentpermlink, now]
  )
}

console.log('commentup Started.')
