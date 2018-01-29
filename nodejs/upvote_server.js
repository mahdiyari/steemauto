const steem = require("steem");
const http = require('http');
var url = require('url');
const hostname = '0.0.0.0';
const port = 123;//port

steem.api.setOptions({ url: 'ws://127.0.0.1:8090' }); //Local private node

const server = http.createServer((req, res) => {
	var params = url.parse(req.url,true).query;
	res.statusCode = 200;
	res.setHeader('Content-Type', 'text/html');
	
	var wif = params.wif;
	var voter = params.voter;
	var author = params.author;
	var permlink = params.permlink;
	var weight = parseInt(params.weight);
	if(wif && voter && author && permlink && weight){
		steem.api.getContentAsync(author, permlink, function(erz, rez) { //checking if post already upvoted or not
			if(!erz && rez){
				var voted = 0;
				for(j in rez['active_votes']){
					if(rez['active_votes'][j].voter == voter){
						voted = 1;
						break;
					}
				}
				if(voted == 0){
					steem.broadcast.vote(wif,voter,author,permlink,weight,function(downerr, result){ //broadcasting upvote
						if(!downerr && result){
							res.end('1');
						}else{
							res.end('0');
						}
					});
				}else{
					res.end('0');
				}
			}else{
				res.end('0');
			}
			
		});
	
	}
});

server.listen(port, hostname, () => { //running server
	console.log(`Server running at http://${hostname}:${port}/`);
});
