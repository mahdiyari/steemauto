const fetch = require('node-fetch')
const config = require('../config')
// this function will send post and voter information to another app to upvote
let i = 1
const upvote = async (voter, author, permlink, weight) => {
  try {
    i++
    if (i < 1) i = 1
    setTimeout(async () => {
      // Upvote server url for handling upvotes
      const url = config.nodejssrv + ':7412/' +
        '?wif=' + config.wifkey +
        '&voter=' + voter +
        '&author=' + author +
        '&permlink=' + permlink +
        '&weight=' + weight
      await fetch(url)
      i--
    }, i * 30)
  } catch (e) {
    throw new Error(e)
  }
}

module.exports = upvote
