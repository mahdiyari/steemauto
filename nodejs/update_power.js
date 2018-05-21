//const steem = require('steem');
const config = require('./config');
const mysql = require('mysql');
var XMLHttpRequest = require("xmlhttprequest").XMLHttpRequest;
var con = mysql.createConnection({host: config.db.host,user: config.db.user,password: config.db.pw,database: config.db.name,charset: "utf8mb4"});

setInterval(function(){
	con.query('SELECT `user`,`id` FROM `users`', function (error, results, fields) {
			checkvotingpower(results);
//console.log(results);
	});
},60000);//every minutes

function checkvotingpower(user){
  var xmlhttp = new XMLHttpRequest();
  xmlhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      var arrayusers = JSON.parse(this.responseText).req;
//console.log(arrayusers);
     	for(i in arrayusers){
        updatepower(arrayusers[i].id,arrayusers[i].power);
      }
    }
  };
  xmlhttp.open("POST", config.nodejssrv+':3683' , true);
  xmlhttp.send(JSON.stringify({req:user}));

	return 1;
}


function updatepower(id,powernow){
	con.query('UPDATE `users` SET `current_power`="'+powernow+'" WHERE `id`='+id, function (error, results, fields) {});
	return 1;
}

setInterval(function (){
	con.query('SELECT 1', function (error, results, fields) {});
},5000);
