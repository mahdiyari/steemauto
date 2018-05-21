const config = require('./config');
const mysql = require('mysql');
var con = mysql.createConnection({host: config.db.host,user: config.db.user,password: config.db.pw,database: config.db.name,charset: "utf8mb4"});
const steem = require("steem");
const http = require('http');
var url = require('url');
const hostname = '127.0.0.1';
const port = 3693;

steem.api.setOptions({ url: config.rpc });

const server = http.createServer((req, res) => {
	var params = url.parse(req.url,true).query;
	res.statusCode = 200;
	res.setHeader('Content-Type', 'application/json');

	var user = params.user;
	if(user){
    steem.api.getAccountsAsync([user], function(err, result){
  		if(!err && result){
  			var u = result[0];
  			var now = new Date();
  			var n = now.getTime()/1000;
  			var last = new Date(u.last_vote_time+'z');
  			var l = last.getTime()/1000;
  			var power = u.voting_power/100 + (parseFloat(n-l)/4320);
  			var powernow = power.toFixed(2);
  			if(powernow > 100){
  				powernow = 100;
  			}
        if(powernow){
          con.query('UPDATE `users` SET `current_power`="'+powernow+'" WHERE `user`="'+user+'"', function (error, results, fields) {
            if(!error){
              res.end(JSON.stringify({result:1}));
            }else{
              res.end(JSON.stringify({result:0}));
            }
          });
        }
  		}
  	});

	}
});

server.listen(port, hostname, () => {
	console.log(`Server running at http://${hostname}:${port}/`);
});

setInterval(function (){
	con.query('SELECT 1', function (error, results, fields) {});
},5000);
