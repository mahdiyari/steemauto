const con = require('./mysql')
const upvote = require('./helpers/broadcastUpvote')
const checkLimits = require('./helpers/checkLimits')

// we will check database every 10 seconds to broadcast delayed upvotes
setInterval(async () => {
  try {
    const nowdate = new Date()
    const nowsec = nowdate.getTime() / 1000
    const now = Math.floor(nowsec)
    const results = await con.query(
      'SELECT EXISTS(SELECT `id` FROM `upvotelater` WHERE `time`<?)',
      [now]
    )
    for (let i in results[0]) {
      const exists = results[0][i]
      // is there any data in the database?
      if (exists === 1) {
        const results = await con.query(
          'SELECT `voter`,`author`,`permlink`,`weight`,`id` FROM `upvotelater` WHERE `time`<?',
          [now]
        )
        for (let i in results) {
          const voter = results[i].voter
          const author = results[i].author
          const permlink = results[i].permlink
          const weight = results[i].weight
          const id = results[i].id
          // first check limitations
          // then broadcast upvote
          // and remove that upvote from database
          const result = await checkLimits(voter, author, permlink, weight)
          // broadcast upvote if user detail is not limited
          if (result) upvote(voter, author, permlink, weight)
          removing(id)
        }
      }
    }
  } catch (e) {
    throw new Error(e)
  }
}, 10000)

const removing = async (id) => {
  con.query(
    'DELETE FROM `upvotelater` WHERE `upvotelater`.`id` = ?',
    [id]
  ).catch(e => {
    throw new Error(e)
  })
}

console.log('Delay upvoting Started.')
