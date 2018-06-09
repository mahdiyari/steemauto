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
var arryou =[];
var ix =0;
var ic =0;
const server = http.createServer((req, res) => {
  ix =0;
  ic =0;
  arrayusers = [];
  arryou =[];
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
      	for(let i in you){
          arryou.push(you[i].user);
          ix = ix+1;
      	}
        if(arryou){
          checkpower(arryou);
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
function checkpower(users) {
  steem.api.getAccountsAsync(users, function(err, result){
    if(!err && result){
      for(let i in result){
        let u = result[i];
        let user = u.name;
        let now = new Date();
        let n = now.getTime()/1000;
        let last = new Date(u.last_vote_time+'z');
        let l = last.getTime()/1000;
        let power = u.voting_power/100 + (parseFloat(n-l)/4320);
        let powernow = power.toFixed(2);
        if(powernow > 100){
          powernow = 100;
        }
        let delegated = parseInt(u.delegated_vesting_shares.replace("VESTS","")); // VESTS
        let received = parseInt(u.received_vesting_shares.replace("VESTS","")); // VESTS
        let vesting = parseInt(u.vesting_shares.replace("VESTS","")); // VESTS
        let totalvest = vesting + received - delegated;
        let sp = totalvest * (tvfs/tvs);
        sp = sp.toFixed(2);
        let uid = u.id;
        let obj = {user:user,power:powernow,sp:sp,uid:uid};
        ic = ic+1;
        arrayusers.push(obj);
      }
    }
  });
}
server.listen(port, hostname, () => {
	console.log(`Server running at http://${hostname}:${port}/`);
});
