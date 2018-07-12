// const config = require('./steemauto/config')
// const steem = require("steem");
const dsteem = require('dsteem')
const http = require('http')
const url = require('url')
const hostname = process.env.HOST || '0.0.0.0'
const port = process.env.PORT || 7412
// steem.api.setOptions({ url: config.rpc });
const client = new dsteem.Client('http://127.0.0.1:8090')

// We will handle upvotes by this function
const upvote = async (wif, voter, author, permlink, weight) => {
  try {
    let key = dsteem.PrivateKey.from(wif)
    await client.broadcast.vote({
      voter,
      author,
      permlink,
      weight
    }, key)
    return 1
  } catch (e) {
    return 0
  }
}

// This server will receive wif, voter, author, permlink and weight
// Then will return result of upvote
const server = http.createServer((req, res) => {
  let params = url.parse(req.url, true).query
  res.statusCode = 200
  res.setHeader('Content-Type', 'application/json')

  let wif = params.wif
  let voter = params.voter
  let author = params.author
  let permlink = params.permlink
  let weight = parseInt(params.weight)
  if (weight <= 0) weight = 1
  if (wif && voter && author && permlink && weight) {
    // We will check voters and post date
    // We will skip already upvoted posts and posts older than 6.5 days
    client.database.call('get_content', [author, permlink])
      .then((result) => {
        let datee = new Date()
        let secondss = datee.getTime() / 1000
        let datee1 = new Date(result.created + 'Z')
        let secondss1 = datee1.getTime() / 1000
        if ((secondss - secondss1) < 561600) {
          // 561600 seconds = 6.5 days
          let voted = 0
          for (let j in result['active_votes']) {
            if (result['active_votes'][j].voter === voter) {
              voted = 1
              break
            }
          }
          if (voted === 0) {
            upvote(wif, voter, author, permlink, weight)
              .then(result => {
                res.end(JSON.stringify({
                  result: 1,
                  reason: 'up done'
                }))
              })
              .catch(err => {
                res.end(JSON.stringify({
                  result: 0,
                  reason: 'up fail',
                  err
                }))
              })
          } else {
            res.end(JSON.stringify({
              result: 0,
              reason: 'already voted'
            }))
          }
        } else {
          res.end(JSON.stringify({
            result: 0,
            reason: 'too old!'
          }))
        }
      }).catch(err => {
        res.end(JSON.stringify({
          result: 0,
          reason: 'rpc node fail',
          err
        }))
      })
  }
})
server.listen(port, hostname, () => {
  console.log(`Server running at http://${hostname}:${port}/`)
})
