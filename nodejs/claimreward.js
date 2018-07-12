const config = require('./config')
var steem = require('steem')
var mysql = require('mysql')
var con = mysql.createConnection({host: config.db.host, user: config.db.user, password: config.db.pw, database: config.db.name, charset: 'utf8mb4'})
var wifkey = config.wifkey

steem.api.setOptions({ url: config.rpc })

var users = []// fanbase
var users1 = []// trailers
var users2 = []// commentupvote

steem.api.streamBlockNumber(function (err1, newestblock) {
  // console.log(newestblock);
})

// Claim Reward function - included 3 seconds delay!
var delay2 = 0
function claimrewards (username) {
  delay2 = delay2 + 1
  setTimeout(function () {
    steem.api.getAccountsAsync([username], function (err, result) {
      if (!err && result) {
        var res = result[0]
        var sbd = res['reward_sbd_balance']
        var steemm = res['reward_steem_balance']
        var vest = res['reward_vesting_balance']
        if (parseFloat(vest) > 0) {
          broadcastclaim(username, sbd, steemm, vest)
        }
      } else {
        // console.log('err in claim1.');
      }
    })
    delay2 = delay2 - 1
  }, 100 * delay2)

  return 1
}

var delay3 = 0
function broadcastclaim (username, sbd, steemm, vest) {
  delay3 = delay3 + 1
  setTimeout(function () {
    steem.broadcast.claimRewardBalance(wifkey, username, steemm, sbd, vest, function (err, result) {
      if (err) {
        // console.log('err in claim2.');
      } else {
        // console.log('claim done.');
      }
    })
    delay3 = delay3 - 1
  }, 100 * delay3)
  return 1
}

// Claiming Rewards Every 15 Mnutes
setInterval(function () {
  con.query('SELECT EXISTS(SELECT * FROM `users` WHERE `claimreward` = "1")', function (error, results, fields) {
    for (i in results) {
      for (j in results[i]) {
        if (results[i][j] == 1) {
          con.query('SELECT * FROM `users` WHERE `claimreward` = "1"', function (error, results, fields) {
            for (k in results) {
              var userr = results[k].user
              claimrewards(userr)
              // console.log('user to claim');
            }
          })
        }
      }
    }
  })
}, 900000)

setInterval(function () {
  con.query('SELECT 1', function (error, results, fields) {})
}, 5000)
console.log('claimreward Started.')
