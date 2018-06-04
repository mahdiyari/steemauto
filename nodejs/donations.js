const config = require('./config');
var steem = require('steem');
var mysql = require('mysql');
var con = mysql.createConnection({host: config.db.host,user: config.db.user,password: config.db.pw,database: config.db.name,charset: "utf8mb4"});
steem.api.setOptions({ url: config.rpc });

steem.api.streamBlockNumber(function (err1, newestblock) {
    console.log(newestblock);
});

// Streaming Blocks
function startstream(){
	steem.api.streamOperations(function (err2, blockops) {
		if(!err2){
			running = 1;
			var op = blockops;
			if(op[0]=='transfer' && op[1].to == 'steemauto'){
				if((op[1].amount).match('SBD')){
					addtodonations(parseFloat(op[1].amount),'SBD');
				}else if((op[1].amount).match('STEEM')){
          addtodonations(parseFloat(op[1].amount),'STEEM');
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
function addtodonations(amount,type){
	con.query('UPDATE `donations` SET `amount`=`amount`+"'+amount+'" WHERE `month`=1 AND `type`="'+type+'"', function (error, results, fields) {
    console.log('donation '+amount+' '+type+' saved.');
	});

	return 1;
}

setInterval(function () {
	con.query('SELECT 1', function (error, results, fields) {});
}, 5000);
