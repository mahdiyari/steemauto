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

con.query('SELECT `user` FROM `trailers` WHERE `followers`>0', function (error, results, fields) {
	for(i in results){
		users1.push(results[i].user);
	}
});


// Updating Users List Every 10 Minutes
setInterval(function(){
	try{
		con.query('SELECT `user` FROM `trailers` WHERE `followers`>0', function (error, results, fields) {
			var busers=[];
			for(i in results){
				busers.push(results[i].user);
			}
			users1 = busers;
		});
	}
	catch(e){
		console.log('error in updating users'+e);
	}
},600000);

var regex = /^re\-/;
var running;

// Streaming Blocks
function startstream(){
	steem.api.streamOperations(function (err2, blockops) {
		if(!err2){
			running = 1;
			var op = blockops;
			if(op[0]=='vote' && !op[1].permlink.match(regex) && op[1].weight > 0){
				if(op[1].voter != op[1].author || op[1].voter == 'steemauto'){
					if(users1.indexOf(op[1].voter) > -1){
						trailupvote(op[1].voter,op[1].author,op[1].permlink,op[1].weight);
						console.log('trail vote detected by: '+op[1].voter);
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
//it seems failed! commenting for now
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
function checkpowerlimit(voter,author,permlink,weight,votingway,trailer){
	con.query('SELECT `current_power`,`limit_power`,`sp` FROM `users` WHERE `user`="'+voter+'"', function (error, results, fields) {
		for(i in results){
			var powernow = results[i].current_power;
			var powerlimit = results[i].limit_power;
			var sp = results[i].sp;
			if(powernow > powerlimit){
				if(((powernow/100)*(weight/10000)*sp) > 1.5){ //Don't broadcast upvote if sp*weight*power < 1.5
					upvote(voter,author,permlink,weight,votingway,trailer);
				}else{
					//console.log('Low SP');
				}
			}else{
				//console.log('power is under limit user '+voter);
			}
		}
	});

	return 1;
}

// Upvote function - included 0 seconds delay!
var delay = 0;
function upvote(voter,author,permlink,weight,votingway,trailer){
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			if(JSON.parse(this.responseText).result == 1) {
				console.log('up done');
			}else{
				console.log(JSON.parse(this.responseText).reason);
			}
		}
	};
	xmlhttp.open("GET", config.nodejssrv+':7412/?wif='+wifkey+'&voter='+voter+'&author='+author+'&permlink='+permlink+'&weight='+weight , true);
	xmlhttp.send();

	return 1;
}



// Upvoting Curation Trail Followers
var trailupvote = function(userr,author,permlink,fweight){

	try{
		steem.api.getContentAsync(author, permlink, function(erz, rez) {
			if(!erz){
				var datee1 = new Date(rez.created+'Z');
				var secondss1 = datee1.getTime()/1000;
				var datee = new Date();
				var secondss = datee.getTime()/1000;
				var now = Math.floor(secondss);
				if(secondss - secondss1 < 518400){

					con.query('SELECT `follower`,`weight`,`aftermin`,`votingway` FROM `followers` WHERE `trailer` = "'+userr+'" AND `enable`="1"', function (error, results, fields) {
						for(i in results){
							var follower = results[i].follower;
							var voted = 0;
							for(j in rez['active_votes']){
								if(rez['active_votes'][j].voter == follower){
									voted = 1;
									break;
								}
							}
							if(voted == 0){
								var weight = results[i].weight;
								var aftermin = results[i].aftermin;
								var votingway = results[i].votingway;
								if(votingway == 1){
									weight = parseInt((weight/10000)*fweight);
								}
								var secs = aftermin*60;
								if(aftermin > 0){
									console.log('trail to delay');
									var time = parseInt(now+(aftermin*60));
									time = Math.floor(time);
									if(votingway == 1){
										con.query('INSERT INTO `upvotelater`(`voter`, `author`, `permlink`, `weight`, `time`,`trail_fan`,`trailer`) VALUES ("'+follower+'","'+author+'","'+permlink+'","'+weight+'","'+time+'","0","'+userr+'")', function (error, results, fields) {
										});
									}else{
										con.query('INSERT INTO `upvotelater`(`voter`, `author`, `permlink`, `weight`, `time`,`trail_fan`,`trailer`) VALUES ("'+follower+'","'+author+'","'+permlink+'","'+weight+'","'+time+'","1","'+userr+'")', function (error, results, fields) {
										});
									}
								}else{
									checkpowerlimit(follower,author,permlink,weight,votingway,userr);
									console.log('trail to up');
								}
							}
						}
					});
				}
			}
		});
	}
	catch(e){
		console.log('error in trail upvote.');
	}
}

function sleep(ms) { //sleeping 3 sec
  return new Promise(resolve => setTimeout(resolve, ms));
}


setInterval(function () {
	con.query('SELECT 1', function (error, results, fields) {});
}, 5000);
console.log('Trail Started.');
