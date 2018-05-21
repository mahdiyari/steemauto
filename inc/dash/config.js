var config = {};
config.db = {};
config.db.pw = 'Super Secret Mysql Password';
config.db.user = 'root';
config.db.host = '127.0.0.1';
config.db.name = 'steemauto';
config.wifkey = 'Super Secret @steemauto Posting wif';
config.rpc = 'ws://127.0.0.1:8090'; //websocket url (needed)
config.rpchttp = 'http://127.0.0.1:8090'; // RPC url (needed)
config.nodejssrv = 'http://127.0.0.1'; //if you are running upvote code in other server, change it

module.exports = config;
