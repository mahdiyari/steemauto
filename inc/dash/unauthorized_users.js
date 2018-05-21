const config = require('./config');
const steem = require('steem');
const mysql = require("mysql");
var counter = 0;
var con = mysql.createConnection({host: config.db.host,user: config.db.user,password: config.db.pw,database: config.db.name,charset: "utf8mb4"});
steem.api.setOptions({ url: config.rpc });

setInterval(function(){
  counter = 0;
  con.query('SELECT `user` FROM `users`', function (error, results, fields) {
    for(i in results){
      setpowerlimit(results[i].user);
    }
  });
},21600000); //every 6 hours

function setpowerlimit(user){
  steem.api.getAccountsAsync([user],function(err,result){
    var auths = result[0].posting.account_auths;
    var isauth = 0;
    for(i in auths){
      if(auths[i][0] == 'steemauto'){
        isauth = 1;
      }
    }
    setTimeout(function(){
      if(isauth == 0){//disabling all possible features for unauthorized user
        con.query('UPDATE `users` SET `limit_power`=100 WHERE `user`="'+user+'"', function (error, results, fields) {});
        con.query('UPDATE `followers` SET `enable`=0 WHERE `follower`="'+user+'"', function (error, results, fields) {});
        con.query('UPDATE `fanbase` SET `enable`=0 WHERE `follower`="'+user+'"', function (error, results, fields) {});
        con.query('UPDATE `commentupvote` SET `enable`=0 WHERE `user`="'+user+'"', function (error, results, fields) {});
        con.query('UPDATE `posts` SET `status`=2 WHERE `user`="'+user+'"', function (error, results, fields) {});
      }
    },1000);
  });
}

setInterval(function () {
	con.query('SELECT 1', function (error, results, fields) {});
}, 5000);
