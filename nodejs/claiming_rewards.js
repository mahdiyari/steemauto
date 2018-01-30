 var steem = require('steem');
var mysql = require('mysql');
var con = mysql.createConnection({host: "127.0.0.1",user: "steemauto",password: "1234",database: "steemauto"});
var wifkey = 'wif'; //posting wif

steem.api.setOptions({ url: 'ws://Node' });

var users=[];//fanbase
var users1=[];//trailers
var users2=[];//commentupvote

steem.api.streamBlockNumber(function (err1, newestblock) {
    console.log(newestblock);
});

// Claim Reward function - included 0.1 seconds delay!
var delay2 = 0;
function claimrewards(username){
	delay2 = delay2 +1;
	setTimeout(function(){
		steem.api.getAccountsAsync([username], function(err, result){
			if(!err && result){
				var res = result[0];
				var sbd = res["reward_sbd_balance"];
				var vest = res["reward_vesting_balance"];
				if(parseFloat(vest) > 0){
					broadcastclaim(username,sbd,vest);
				}
			}else{
				console.log('err in claim1.');
			}
			
		});
		delay2 = delay2 -1;
	},100*delay2);
	
	return 1;
}

//broadcasting claim reward with adding to queue
//queue will prevent blockchain spamming
var delay3 = 0;
function broadcastclaim(username,sbd,vest){
	delay3 = delay3 +1;
	setTimeout(function(){
		steem.broadcast.claimRewardBalance(wifkey,username,'0.000 STEEM',sbd,vest, function(err, result) {
			if(err){
				console.log('err in claim2.');
			}else{
				console.log('claim done.');
			}
			
		});
		delay3 = delay3 -1;
	},100*delay3);
	return 1;
}

// Claiming Rewards Every 15 Mnutes
setInterval(function(){
	con.query('SELECT EXISTS(SELECT * FROM `users` WHERE `claimreward` = "1")', function (error, results, fields) {
		for(i in results){
			for(j in results[i]){
				if(results[i][j] == 1){
					con.query('SELECT * FROM `users` WHERE `claimreward` = "1"', function (error, results, fields) {
						for(k in results){
							var userr = results[k].user;
							claimrewards(userr);
							console.log('user to claim');
						}
					});
				}
			}
		}
	});
},900000);

//keep live mysql connection
setInterval(function () {
	con.query('SELECT 1', function (error, results, fields) {});
}, 5000);
