const config = require('./config')
const steem = require('steem')
const mysql = require('mysql')
var XMLHttpRequest = require('xmlhttprequest').XMLHttpRequest
var con = mysql.createConnection({host: config.db.host, user: config.db.user, password: config.db.pw, database: config.db.name, charset: 'utf8mb4'})
var wifkey = config.wifkey
steem.api.setOptions({ url: config.rpc })

// stream block numbers (just for checking if RPC node connection works)

// check scheduled posts every 15 seconds
setInterval(function () {
  var datee = new Date()
  var secondss = datee.getTime() / 1000
  var now = Math.floor(secondss)
  con.query('SELECT EXISTS(SELECT * FROM `posts` WHERE `date`<"' + now + '" AND `status`="0")', function (error, resultz, fields) {
    for (i in resultz) {
      for (j in resultz[i]) {
        if (resultz[i][j]) {
          con.query('SELECT * FROM `posts` WHERE `date`<"' + now + '" AND `status`="0"', function (error, results, fields) {
            for (i in results) {
              var parentAuthor = ''
              var parentPermlink = results[i].maintag
              var author = results[i].user
              var permlink = results[i].permlink
              var title = results[i].title
              var body = results[i].content
              var jsonMetadata = results[i].json
              var upvotepost = results[i].upvote
              var rewardstype = results[i].rewards
              publishpost(parentAuthor, parentPermlink, author, permlink, title, body, jsonMetadata, upvotepost, rewardstype)
            }
          })
        }
      }
    }
  })
}, 20000)

// upvoting function
function upvote (voter, author, permlink, weight) {
  var xmlhttp = new XMLHttpRequest()
  xmlhttp.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      if (JSON.parse(this.responseText).result == 1) {
        // console.log('up done');
      } else {
        // console.log(JSON.parse(this.responseText).reason);
      }
    }
  }
  xmlhttp.open('GET', config.nodejssrv + ':7412/?wif=' + wifkey + '&voter=' + voter + '&author=' + author + '&permlink=' + permlink + '&weight=' + weight, true)
  xmlhttp.send()

  return 1
}

// function for publishing posts
function publishpost (parentAuthor, parentPermlink, author, permlink, title, body, jsonMetadata, upvotepost, rewardstype) {
  steem.broadcast.comment(wifkey, parentAuthor, parentPermlink, author, permlink, title, body, jsonMetadata, function (err, result) {
  	if (result) { // set status to published
  		con.query('UPDATE `posts` SET `status`=1 WHERE `user`="' + author + '" AND `permlink`="' + permlink + '"', function (error, results, fields) {})
      if (rewardstype == 1) {
        steem.broadcast.commentOptions(wifkey, author, permlink, '1000000.000 SBD', 0, true, true, [], function (err, result) {
          // powering up 100%
          if (upvotepost == 1) {
            upvote(author, author, permlink, '10000')
          }
        })
      } else if (rewardstype == 2) {
        steem.broadcast.commentOptions(wifkey, author, permlink, '0.000 SBD', 10000, true, true, [], function (err, result) {
          // decline payout
          if (upvotepost == 1) {
            upvote(author, author, permlink, '10000')
          }
        })
      } else {
        if (upvotepost == 1) {
          upvote(author, author, permlink, '10000')
        }
      }
  	} else { // set status to not published
  		con.query('UPDATE `posts` SET `status`=2 WHERE `user`="' + author + '" AND `permlink`="' + permlink + '"', function (error, results, fields) {})
  	}
  })
}

// keep connection alive
setInterval(function () {
  con.query('SELECT 1', function (error, results, fields) {})
}, 5000)
console.log('schedule Started.')
