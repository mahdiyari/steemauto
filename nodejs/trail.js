const config = require('./config')
const steem = require('steem')
const con = require('./mysql')
const XMLHttpRequest = require('xmlhttprequest').XMLHttpRequest
const wifkey = config.wifkey
steem.api.setOptions({ url: config.rpc })

let users1 = [] // trailers

con.query('SELECT `user` FROM `trailers` WHERE `followers`>0')
  .then(results => {
    for (let i in results) {
      users1.push(results[i].user)
    }
  })
  .catch(err => console.error(err))

// Updating Trails List Every 10 Minutes
setInterval(() => {
  con.query('SELECT `user` FROM `trailers` WHERE `followers`>0')
    .then(results => {
      let busers = []
      for (let i in results) {
        busers.push(results[i].user)
      }
      users1 = busers
    })
    .catch(err => console.error(err))
}, 600000)

const regex = /^re-/

// Streaming Blocks
function startstream () {
  steem.api.streamOperations((err2, blockops) => {
    if (!err2) {
      let op = blockops
      if (op[0] === 'vote' && !op[1].permlink.match(regex) && op[1].weight > 0) {
        if (op[1].voter !== op[1].author || op[1].voter === 'steemauto') {
          if (users1.indexOf(op[1].voter) > -1) {
            trailupvote(op[1].voter, op[1].author, op[1].permlink, op[1].weight)
              .catch(err => console.error(err))
          }
        }
      }
      if (err2) {
        console.error('err in streaming.')
      }
    }
  })
  return 1
}
startstream()

// Check voting power limit then broadcast upvote
const checkpowerlimit = async (voter, author, permlink, weight, votingway, trailer) => {
  let results = await con.query(
    'SELECT `current_power`,`limit_power`,`sp` FROM `users` WHERE `user`=?',
    [voter]
  )
  let powernow = results[0].current_power
  let powerlimit = results[0].limit_power
  let sp = results[0].sp
  if (powernow > powerlimit) {
    // Don't broadcast upvote if sp*weight*power < 1.5 SP
    if (((powernow / 100) * (weight / 10000) * sp) > 1.5) {
      upvote(voter, author, permlink, weight, votingway, trailer)
    } else {
      // console.log('Low SP');
    }
  } else {
    // console.log('power is under limit user '+voter);
  }
}

// Upvote function for handling upvotes
const upvote = (voter, author, permlink, weight, votingway, trailer) => {
  let xmlhttp = new XMLHttpRequest()
  xmlhttp.onreadystatechange = function () {
    if (this.readyState === 4 && this.status === 200) {
      if (JSON.parse(this.responseText).result === 1) {
        // console.log('up done')
      } else {
        // console.log(JSON.parse(this.responseText).reason)
      }
    }
  }
  xmlhttp.open('GET', config.nodejssrv + ':7412/?wif=' + wifkey + '&voter=' + voter + '&author=' + author + '&permlink=' + permlink + '&weight=' + weight, true)
  xmlhttp.send()

  return 1
}

// Upvoting Curation Trail Followers
const trailupvote = async (userr, author, permlink, fweight) => {
  let rez = await steem.api.getContentAsync(author, permlink)
  let datee1 = new Date(rez.created + 'Z')
  let secondss1 = datee1.getTime() / 1000
  let datee = new Date()
  let secondss = datee.getTime() / 1000
  let now = Math.floor(secondss)
  if (secondss - secondss1 < 518400) {
    let results = await con.query(
      'SELECT `follower`,`weight`,`aftermin`,`votingway` FROM `followers` WHERE `trailer` =? AND `enable`="1"',
      [userr]
    )
    for (let i in results) {
      let follower = results[i].follower
      let voted = 0
      for (let j in rez['active_votes']) {
        if (rez['active_votes'][j].voter === follower) {
          voted = 1
          break
        }
      }
      if (voted === 0) {
        let weight = results[i].weight
        let aftermin = results[i].aftermin
        let votingway = results[i].votingway
        if (votingway === 1) {
          weight = parseInt((weight / 10000) * fweight)
        }
        if (aftermin > 0) {
          // User configured to upvote after X minutes
          // console.log('trail to delay')
          let time = parseInt(now + (aftermin * 60))
          time = Math.floor(time)
          if (votingway === 1) {
            con.query(
              'INSERT INTO `upvotelater`(`voter`, `author`, `permlink`, `weight`, `time`,`trail_fan`,`trailer`) VALUES (?,?,?,?,?,"0",?)',
              [follower, author, permlink, weight, time, userr]
            )
          } else {
            con.query(
              'INSERT INTO `upvotelater`(`voter`, `author`, `permlink`, `weight`, `time`,`trail_fan`,`trailer`) VALUES (?,?,?,?,?,"1",?)',
              [follower, author, permlink, weight, time, userr]
            )
          }
        } else {
          // User configured to vote just now
          checkpowerlimit(follower, author, permlink, weight, votingway, userr)
          // console.log('trail to up')
        }
      }
    }
  }
}

console.log('Trail Started.')
