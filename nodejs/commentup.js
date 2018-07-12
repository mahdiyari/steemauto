const config = require('./config')
var steem = require('steem')
var mysql = require('mysql')
var XMLHttpRequest = require('xmlhttprequest').XMLHttpRequest
var con = mysql.createConnection({
  host: config.db.host,
  user: config.db.user,
  password: config.db.pw,
  database: config.db.name,
  charset: 'utf8mb4'
})
var wifkey = config.wifkey

steem.api.setOptions({ url: config.rpc })

var users = []// fanbase
var users1 = []// trailers
var users2 = []// commentupvote

con.query('SELECT DISTINCT `user` FROM `commentupvote`', function (error, results, fields) {
  for (i in results) {
    users2.push(results[i].user)
  }
  console.log('get users.')
})

// Updating Users List Every 10 Minutes
setInterval(function () {
  try {
    con.query('SELECT DISTINCT `user` FROM `commentupvote`', function (error, results, fields) {
      var dusers = []
      for (i in results) {
        dusers.push(results[i].user)
      }
      users2 = dusers
    })
  } catch (e) {
    console.log('error in updating users' + e)
  }
}, 600000)

var regex = /^re\-/
var running

// Streaming Blocks
function startstream () {
  steem.api.streamOperations(function (err2, blockops) {
    if (!err2) {
      running = 1
      var op = blockops
      if (op[0] == 'comment' && op[1].parent_author != '') {
        if (users2.indexOf(op[1].parent_author) > -1) {
          commentupvote(op[1].parent_author, op[1].author, op[1].permlink, op[1].parent_permlink)
          // console.log('comment detected on: '+op[1].parent_author);
        }
      }
      if (err2) {
        console.log('err in streaming.')
      }
    }
  })
  return 1
}
startstream()

// Checking if stream is running or not!
// it seems failed! commenting for now
/*
setInterval(function(){
 running = 0;
 setTimeout(function(){
   if(running == 0){
     startstream();
   }
 },30000);
},100000);
*/
// Check voting power limit
function checkpowerlimit (voter, author, permlink, weight) {
  con.query('SELECT `current_power`,`limit_power`,`sp` FROM `users` WHERE `user`="' + voter + '"', function (error, results, fields) {
    for (i in results) {
      var powernow = results[i].current_power
      var powerlimit = results[i].limit_power
      var sp = results[i].sp
      if (powernow > powerlimit) {
        // Don't broadcast upvote if sp*weight*power < 1.5
        if (((powernow / 100) * (weight / 10000) * sp) > 1.5) {
          upvote(voter, author, permlink, weight)
        } else {
          // console.log('low sp');
        }
      } else {
        // console.log('power is under limit user '+voter);
      }
    }
  })

  return 1
}
// Upvote function - included 0 seconds delay!
var delay = 0
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

function sleep (ms) { // sleeping 3 sec
  return new Promise(resolve => setTimeout(resolve, ms))
}

// Upvoting Comments Automatically //
var delay2 = 0
function commentupvote (userr, commenter, permlink, parentpermlink) {
  try {
    con.query('SELECT EXISTS(SELECT * FROM `commentupvote` WHERE `user` = "' + userr + '" AND `commenter`="' + commenter + '" AND `enable`="1" AND `todayvote`<2)', function (error, results, fields) {
      for (i in results) {
        for (j in results[i]) {
          if (results[i][j] == 1) {
            con.query('SELECT EXISTS(SELECT * FROM `upvotedcomments` WHERE `user` = "' + commenter + '" AND `permlink`="' + parentpermlink + '")', function (error, results, fields) {
              for (o in results) {
                for (p in results[o]) {
                  if (results[o][p] == 0) {
                    con.query('SELECT `weight`,`aftermin` FROM `commentupvote` WHERE `user` = "' + userr + '" AND `commenter`="' + commenter + '" AND `enable`="1" AND `todayvote`<2', async function (error, results, fields) {
                      for (k in results) {
                        var weight = results[k].weight
                        var aftermin = results[k].aftermin
                        var datee = new Date()
                        var secondss = datee.getTime() / 1000
                        var now = Math.floor(secondss)
                        if (aftermin > 0) {
                          var time = parseInt(now + (aftermin * 60))
                          con.query('INSERT INTO `upvotelater`(`voter`, `author`, `permlink`, `weight`, `time`,`trail_fan`) VALUES ("' + userr + '","' + commenter + '","' + permlink + '","' + weight + '","' + time + '","3")',
                            function (error, results, fields) {})
                          con.query('UPDATE `commentupvote` SET `todayvote`=`todayvote`+1 WHERE `user` = "' + userr + '" AND `commenter`="' + commenter + '"',
                            function (error, results, fields) {})
                          con.query('INSERT INTO `upvotedcomments`(`user`, `permlink`,`time`) VALUES ("' + commenter + '","' + parentpermlink + '","' + now + '")',
                            function (error, results, fields) {})
                          // console.log('comment to delay');
                        } else {
                          // console.log('comment to upvote');
                          checkpowerlimit(userr, commenter, permlink, weight)
                          con.query('INSERT INTO `upvotedcomments`(`user`, `permlink`,`time`) VALUES ("' + commenter + '","' + parentpermlink + '","' + now + '")', function (error, results, fields) {
                          })
                          con.query('UPDATE `commentupvote` SET `todayvote`=`todayvote`+1 WHERE `user` = "' + userr + '" AND `commenter`="' + commenter + '"', function (error, results, fields) {
                          })
                        }
                      }
                    })
                  }
                }
              }
            })
          }
        }
      }
    })
  } catch (e) {
    console.log('error in comment upvote.')
  }
}

// keep mysql connection Live
setInterval(function () {
  con.query('SELECT 1', function (error, results, fields) {})
}, 5000)
console.log('commentup Started.')
