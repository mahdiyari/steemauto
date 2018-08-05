/**
 * This file will check limitations on the users
 * one limitation is configured voting power
 * and another limitation is effective SP of each upvote
 */
const call = require('./nodeCall')
const config = require('../config')
const con = require('../mysql')
const isSteemd = config.isSteemd
// we are using isSteemd to change methods for appbase and v0.19.5

let tvfs
let tvs
// Check voting power limit
const updateGlobals = async () => {
  try {
    // get dynamic global propertise for just appbase! (v0.19.10)
    // will need change in other version nodes
    const result = await call(
      isSteemd ? config.steemd : config.rpc,
      isSteemd ? 'condenser_api.get_dynamic_global_properties' : 'get_dynamic_global_properties',
      []
    )
    // on any error, result will be null
    if (!result) return
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
      isSteemd ? config.steemd : config.rpc,
      isSteemd ? 'condenser_api.get_accounts' : 'get_accounts',
      [
        [voter]
      ]
    )
    // on any error, result will be null
    if (!result) return null
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
          return 1
        }
        return null
      }
      return null
    }
  } catch (e) {
    return null
  }
}

module.exports = checkpowerlimit
