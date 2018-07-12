const config = require('./steemauto/config')
// const steem = require("steem");
const dsteem = require('dsteem')
const http = require('http')
var url = require('url')
const hostname = '0.0.0.0'
const port = 7412
// steem.api.setOptions({ url: config.rpc });
var client = new dsteem.Client(config.rpchttp)

function upvote (wif, voter, author, permlink, weight, callback) {
  var key = dsteem.PrivateKey.from(wif)
  client.broadcast.vote({
  	voter: voter,
  	author: author,
  	permlink: permlink,
  	weight: weight
  }, key).then(function (result) {
	   callback(1)
  }, function (error) {
	   callback(0)
  })
}
const server = http.createServer((req, res) => {
  var params = url.parse(req.url, true).query
  res.statusCode = 200
  res.setHeader('Content-Type', 'application/json')

  var wif = params.wif
  var voter = params.voter
  var author = params.author
  var permlink = params.permlink
  var weight = parseInt(params.weight)
  if (weight <= 0) { weight = 1 };
  if (wif && voter && author && permlink && weight) {
    client.database.call('get_content', [author, permlink])
      .then(function (result) {
        var datee = new Date()
        var secondss = datee.getTime() / 1000
        var datee1 = new Date(result.created + 'Z')
        var secondss1 = datee1.getTime() / 1000
        if ((secondss - secondss1) < 561600) {
          var voted = 0
          for (j in result['active_votes']) {
            if (result['active_votes'][j].voter == voter) {
              voted = 1
              break
            }
          }
          if (voted == 0) {
            upvote(wif, voter, author, permlink, weight, function (result) {
              if (result == 1) {
                res.end(JSON.stringify({ result: 0, reason: 'up done' }))
              } else {
                res.end(JSON.stringify({ result: 0, reason: 'up fail' }))
              }
            })
          } else {
            res.end(JSON.stringify({ result: 0, reason: 'already voted' }))
          }
        }
  	}, function (error) {
  	 	res.end(JSON.stringify({ result: 0, reason: 'rpc node fail' }))
  	})
  }
})
server.listen(port, hostname, () => {
  console.log(`Server running at http://${hostname}:${port}/`)
})
