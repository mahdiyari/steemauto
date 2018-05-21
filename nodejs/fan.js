const config = require('./config');
var steem = require('steem');
var mysql = require('mysql');
var XMLHttpRequest = require("xmlhttprequest").XMLHttpRequest;
var con = mysql.createConnection({host: config.db.host,user: config.db.user,password: config.db.pw,database: config.db.name,charset: "utf8mb4"});
var wifkey = config.wifkey;

steem.api.setOptions({ url: config.rpc });

var users=[];//fanbase
var users1=[];//trailers
var users2=[];//commentupvote
con.query('SELECT `fan` FROM `fans` WHERE `followers`>0', function (error, results, fields) {
	for(i in results){
		users.push(results[i].fan);
	}
});

// Updating Users List Every 10 Minutes
setInterval(function(){
	try{
		con.query('SELECT `fan` FROM `fans` WHERE `followers`>0', function (error, results, fields) {
			var nusers=[];
			for(i in results){
				nusers.push(results[i].fan);
			}
			users = nusers;
		});

	}
	catch(e){
		console.log('error in updating users'+e);
	}
},600000);

var regex = /^re\-/;
var running;
var editregex =/^(\@\@+.+\@\@)/;

// Streaming Blocks
function startstream(){
	steem.api.streamOperations(function (err2, blockops) {
		if(!err2){
			running = 1;
			var op = blockops;
			if(op[0]=='comment' && op[1].parent_author == ''){
				if(users.indexOf(op[1].author) > -1){
					if((op[1].body).match(editregex)){
						//console.log('edited post by '+op[1].author);
					}else{
						fanupvote(op[1].author,op[1].permlink);
						//console.log('fan post detected by: '+op[1].author);
					}
				}
			}
			if(err2){
				console.log('err in streaming.');
			}
		}
	});
	return 1;
}
startstream();

// Checking if stream is running or not!
setInterval(function(){
	running = 0;
	setTimeout(function(){
		if(running == 0){
			startstream();
		}
	},30000);
},100000);

// Check voting power limit
function checkpowerlimit(voter,author,permlink,weight){
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
function upvote(voter,author,permlink,weight){
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


// Upvoting Fanbase Followers

var fanupvote = function(author,permlink){

	try{

		var datee = new Date();
		var secondss = datee.getTime()/1000;
		con.query('SELECT `follower`,`weight`,`aftermin` FROM `fanbase` WHERE `fan` = "'+author+'" AND `enable`=1 AND `limitleft`>0', function (error, results, fields) {
			for(i in results){
				var follower = results[i].follower;
				var voted = 0;
				if(voted == 0){
					var weight = results[i].weight;
					var aftermin = results[i].aftermin;
					var datee = new Date();
					var secondss = datee.getTime()/1000;
					var now = Math.floor(secondss);
					if(aftermin > 0){
						var time = parseInt(now+(aftermin*60));
						con.query('INSERT INTO `upvotelater`(`voter`, `author`, `permlink`, `weight`, `time`,`trail_fan`) VALUES ("'+follower+'","'+author+'","'+permlink+'","'+weight+'","'+time+'","2")', function (error, results, fields) {});
						con.query('UPDATE `fanbase` SET `limitleft`=`limitleft`-1 WHERE `fan`="'+author+'" AND `follower`="'+follower+'"', function (error, results, fields) {});
						//console.log('fan to delay');
					}else{
						checkpowerlimit(follower,author,permlink,weight);
						con.query('UPDATE `fanbase` SET `limitleft`=`limitleft`-1 WHERE `fan`="'+author+'" AND `follower`="'+follower+'"', function (error, results, fields) {});
						//console.log('fan to up');
					}
				}
			}

		});

	}
	catch(e){
		console.log('error in fan upvote.'+e);
	}
}

setInterval(function () {
	con.query('SELECT 1', function (error, results, fields) {});
}, 5000);
console.log('Fan Started.');
