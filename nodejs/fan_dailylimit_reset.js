const config = require('./config');
var mysql = require('mysql');
var con = mysql.createConnection({host: config.db.host,user: config.db.user,password: config.db.pw,database: config.db.name,charset: "utf8mb4"});
var CronJob = require('cron').CronJob;
var job = new CronJob('0 0 * * *', function() {
		con.query('UPDATE `fanbase` SET `limitleft`=`dailylimit`', function (error, results, fields) {
			console.log('query done.');
		});
		con.query('UPDATE `commentupvote` SET `todayvote`=0', function (error, results, fields) {
			//
		});
  }, function () {
    console.log('I think cron is done is done.');
  },
  true, /* Start the job right now */
  'UTC' /* Time zone of this job. */
);

setInterval(function () {
	con.query('SELECT 1', function (error, results, fields) {});
}, 5000);
