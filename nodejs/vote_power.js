const config = require('./config');
const steem = require("steem");
const http = require('http');
var url = require('url');
const hostname = '0.0.0.0';
const port = 3683;
var tvfs;
var tvs;
steem.api.setOptions({ url: config.rpc2 });
var arrayusers = [];
var ix =0;
var ic =0;
const server = http.createServer((req, res) => {
  ix =0;
  ic =0;
  arrayusers = [];
  if (req.method == 'POST') {
    var body = '';
  }
  req.on('data', function (data) {
    body += data;
  });

  req.on('end', function () {
		steem.api.getDynamicGlobalProperties(function(e, t) {
      if(!e && t){
        tvfs = parseInt(t.total_vesting_fund_steem.replace("STEEM",""));
  			tvs = parseInt(t.total_vesting_shares.replace("VESTS",""));
        var you = JSON.parse(body).req;
      	for(i in you){
      		checkpower(you[i].user,you[i].id);
          ix = ix+1;
      	}
        res.writeHead(200, {'Content-Type': 'application/json'});
        var int = setInterval(function () {
          if(ic == ix){
            res.end(JSON.stringify({req:arrayusers}));
            clearInterval(int);
          }console.log(ic,ix);
        }, 100);
      }
		});
  });
});
function checkpower(user,id) {
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
      var delegated = parseInt(u.delegated_vesting_shares.replace("VESTS","")); // VESTS
  		var received = parseInt(u.received_vesting_shares.replace("VESTS","")); // VESTS
  		var vesting = parseInt(u.vesting_shares.replace("VESTS","")); // VESTS
  		var totalvest = vesting + received - delegated;
      var sp = totalvest * (tvfs/tvs);
      sp = sp.toFixed(2);
      var obj = {user:user,id:id,power:powernow,sp:sp};
      ic = ic+1;
      arrayusers.push(obj);
    }
  });
}
server.listen(port, hostname, () => {
	console.log(`Server running at http://${hostname}:${port}/`);
});
