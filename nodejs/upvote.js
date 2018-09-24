const config = require('./steemauto/config')
// const dsteem = require("dsteem")
const steem = require('steem')
const http = require('http')
const url = require('url')
const hostname = process.env.HOST || '0.0.0.0'
const port = process.env.PORT || 7412
steem.api.setOptions({ useAppbaseApi: true, url: config.steemd })
// const client = new dsteem.Client('http://127.0.0.1:8090')

// We will handle upvotes by this function
let i = 1
const upvote = async (wif, voter, author, permlink, weight) => {
  i++
  if (i < 1) i = 1
  try {
    setTimeout(async () => {
      // let key = dsteem.PrivateKey.from(wif)
      await steem.broadcast.voteAsync(
        wif,
        voter,
        author,
        permlink,
        weight
      )
      i--
    }, 40 * i)
    return 1
  } catch (e) {
    i--
    return 0
  }
}

// This server will receive wif, voter, author, permlink and weight
// Then will return result of upvote
let k = 1
const server = http.createServer((req, res) => {
  k++
  if (k < 1) k = 1
  setTimeout(async () => {
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
      steem.api.getContentAsync(author, permlink)
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
                  k--
                  res.end(JSON.stringify({
                    result: 1,
                    reason: 'up done'
                  }))
                })
                .catch(err => {
                  k--
                  res.end(JSON.stringify({
                    result: 0,
                    reason: 'up fail',
                    err
                  }))
                })
            } else {
              k--
              res.end(JSON.stringify({
                result: 0,
                reason: 'already voted'
              }))
            }
          } else {
            k--
            res.end(JSON.stringify({
              result: 0,
              reason: 'too old!'
            }))
          }
        }).catch(err => {
          k--
          res.end(JSON.stringify({
            result: 0,
            reason: 'rpc node fail',
            err
          }))
        })
    }
  }, k * 40)
})
server.listen(port, hostname, () => {
  console.log(`Server running at http://${hostname}:${port}/`)
})
