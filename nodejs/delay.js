const config = require('./config')
const con = require('./mysql')
const fetch = require('node-fetch')
const call = require('./helpers/nodeCall')

const wifkey = config.wifkey

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
          checkpowerlimit(voter, author, permlink, weight)
          removing(id)
        }
      }
    }
  } catch (e) {
    throw new Error(e)
  }
}, 10000)

let tvfs
let tvs
// Check voting power limit
// Since this function is same in the 3 files, soon we will export it to another file
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

// Check limitations
// Since this function is same in the 3 files, soon we will export it to another file
const checkpowerlimit = async (voter, author, permlink, weight, type) => {
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
      // calculating voting power to check limitation
      const u = result[0]
      const now = new Date()
      const n = now.getTime() / 1000
      const last = new Date(u.last_vote_time + 'z')
      const l = last.getTime() / 1000
      const power = u.voting_power / 100 + (parseFloat(n - l) / 4320)
      let powernow = power.toFixed(2)
      if (powernow > 100) powernow = 100
      // calculating total SP to check limitation
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
// Upvote function
// Since this function is same in the 3 or more files, soon we will export it to another file
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

const removing = async (id) => {
  con.query(
    'DELETE FROM `upvotelater` WHERE `upvotelater`.`id` = ?',
    [id]
  ).catch(e => {
    throw new Error(e)
  })
}

console.log('Delay upvoting Started.')
