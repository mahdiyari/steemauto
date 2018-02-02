var steem = require('steem');
var mysql = require('mysql');
var XMLHttpRequest = require("xmlhttprequest").XMLHttpRequest;
var con = mysql.createConnection({host: "127.0.0.1",user: "steemauto",password: "1234",database: "steemauto"});
var wifkey = 'WIF'; //Posting wif
var server = 'https://secret_server'; //private upvoting server

steem.api.setOptions({ url: 'ws://NODE' }); //steem node (RPC or websocket)

//streaming block numbers
steem.api.streamBlockNumber(function (err1, newestblock) {
    console.log(newestblock);
});

// Check voting power limit
function checkpowerlimit(voter,author,permlink,weight,type){
	con.query('SELECT `current_power`,`limit_power` FROM `users` WHERE `user`="'+voter+'"', function (error, results, fields) {
		for(i in results){
			var powernow = results[i].current_power;
			var powerlimit = results[i].limit_power;
			if(powernow > powerlimit){
				upvote(voter,author,permlink,weight);
			}else{
				console.log('power is under limit user '+voter);
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
			if(this.responseText == 1){
				console.log('up done');
			}else if(this.responseText == 0){
				console.log('err in up.');
			}
		}
	};
	xmlhttp.open("GET", server+'/?wif='+wifkey+'&voter='+voter+'&author='+author+'&permlink='+permlink+'&weight='+weight , true);
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
								
								checkpowerlimit(voter,author,permlink,weight,type); //upvote
								
								console.log('delaied to up.');
								removing(id); //remove from queue
								
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
},30000); // checking every 30 secons

function removing(id){ //remove function
	con.query('DELETE FROM `upvotelater` WHERE `upvotelater`.`id` = "'+id+'"', function (error, results, fields) {});
	return 1;
}

setInterval(function () { // keep mysql connection live
	con.query('SELECT 1', function (error, results, fields) {});
}, 5000);
