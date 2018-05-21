const config = require('./config');
var steem = require('steem');
var mysql = require('mysql');
var XMLHttpRequest = require("xmlhttprequest").XMLHttpRequest;
var con = mysql.createConnection({host: config.db.host,user: config.db.user,password: config.db.pw,database: config.db.name,charset: "utf8mb4"});
var wifkey = config.wifkey;

steem.api.setOptions({ url: config.rpc });

// Check voting power limit
function checkpowerlimit(voter,author,permlink,weight,type){
 con.query('SELECT `current_power`,`limit_power` FROM `users` WHERE `user`="'+voter+'"', function (error, results, fields) {
   for(i in results){
     var powernow = results[i].current_power;
     var powerlimit = results[i].limit_power;
     if(powernow > powerlimit){
       upvote(voter,author,permlink,weight);
     }else{
       //console.log('power is under limit user '+voter);
     }
   }
 });

 return 1;
}
// Upvote function - included 0 seconds delay!
var delay = 0;
function upvote(voter,author,permlink,weight,type){
  var xmlhttp = new XMLHttpRequest();
  xmlhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      if(JSON.parse(this.responseText).result == 1) {
        //console.log('up done');
      }else{
        //console.log(JSON.parse(this.responseText).reason);
      }
    }
  };
  xmlhttp.open("GET", config.nodejssrv+':7412/?wif='+wifkey+'&voter='+voter+'&author='+author+'&permlink='+permlink+'&weight='+weight , true);
  xmlhttp.send();

 return 1;
}


// Upvoting After a Delay
setInterval(function(){
 try{
   var datee = new Date();
   var secondss = datee.getTime()/1000;
   var now = Math.floor(secondss);
   con.query('SELECT EXISTS(SELECT * FROM `upvotelater` WHERE `time`<"'+now+'")', function (error, results, fields) {
     for(k in results){
       for(j in results[k]){
         var x = results[k][j];
         if(x == 1){
           con.query('SELECT * FROM `upvotelater` WHERE `time`<"'+now+'"', function (error, results, fields) {
             for(i in results){
               var voter = results[i].voter;
               var author = results[i].author;
               var permlink = results[i].permlink;
               var weight = results[i].weight;
               var id = results[i].id;
               var type = results[i].trail_fan;

               checkpowerlimit(voter,author,permlink,weight,type);

               //console.log('delaied to up.');
               removing(id);

             }
           });
         }
       }
     }
   });
 }
 catch(e){
   console.log('error in Delay Upvoting.');
 }
},10000);

function removing(id){
 con.query('DELETE FROM `upvotelater` WHERE `upvotelater`.`id` = "'+id+'"', function (error, results, fields) {});
 return 1;
}

setInterval(function () {
 con.query('SELECT 1', function (error, results, fields) {});
}, 5000);
console.log('Delay Started.');
