const config = require('./config')
const steem = require('steem')
const con = require('./mysql')
const XMLHttpRequest = require('xmlhttprequest').XMLHttpRequest
const wifkey = config.wifkey
steem.api.setOptions({ url: 'https://api.steemit.com' })

// stream block numbers (just for checking if RPC node connection works)

// check scheduled posts every 15 seconds
setInterval(async () => {
  try {
    const nowdate = new Date()
    const nowsec = nowdate.getTime() / 1000
    const now = Math.floor(nowsec)
    const resultz = await con.query(
      'SELECT EXISTS(SELECT * FROM `posts` WHERE `date`<? AND `status`="0")',
      [now]
    )
    for (let i in resultz) {
      for (let j in resultz[i]) {
        if (resultz[i][j]) {
          const results = await con.query(
            'SELECT * FROM `posts` WHERE `date`<? AND `status`="0"',
            [now]
          )
          for (let i in results) {
            const parentAuthor = ''
            const parentPermlink = results[i].maintag
            const author = results[i].user
            const permlink = results[i].permlink
            const title = results[i].title
            const body = results[i].content
            const jsonMetadata = results[i].json
            const upvotepost = Number(results[i].upvote)
            const rewardstype = Number(results[i].rewards)
            const beneficiarytype = Number(results[i].beneficiary)
            publishpost(parentAuthor, parentPermlink, author, permlink, title, body, jsonMetadata, upvotepost, rewardstype, beneficiarytype)
          }
        }
      }
    }
  } catch (e) {
    throw new Error(e)
  }
}, 20000)

// upvoting function
const upvote = (voter, author, permlink, weight) => {
  var xmlhttp = new XMLHttpRequest()
  xmlhttp.onreadystatechange = function () {
    if (this.readyState === 4 && this.status === 200) {
      if (JSON.parse(this.responseText).result === 1) {
        console.log('up done')
      } else {
        // console.log(JSON.parse(this.responseText).reason);
      }
    }
  }
  xmlhttp.open('GET', config.nodejssrv + ':7412/?wif=' + wifkey + '&voter=' + voter + '&author=' + author + '&permlink=' + permlink + '&weight=' + weight, true)
  xmlhttp.send()

  return 1
}

// function for publishing posts
const publishpost = async (parentAuthor, parentPermlink, author, permlink, title, body, jsonMetadata, upvotepost, rewardstype, beneficiarytype) => {
  try {
    // Thanks to @stoodkev
    let operations = [
      ['comment',
        {
          parent_author: parentAuthor,
          parent_permlink: parentPermlink,
          author: author,
          permlink: permlink,
          title: title,
          body: body,
          json_metadata: jsonMetadata
        }
      ]
    ]
    if (beneficiarytype > 0 || rewardstype === 1 || rewardstype === 2) {
      let sbdpercent = 10000
      let sbdaccept = '100000.000 SBD'
      let beneficiaries = []
      if (rewardstype === 1) {
        sbdpercent = 0
      }
      if (rewardstype === 2) {
        sbdaccept = '0.000 SBD'
      }
      if (beneficiarytype > 0) {
        beneficiaries.push({
          account: 'steemauto',
          weight: 100 * beneficiarytype
        })
      }
      operations.push(
        ['comment_options', {
          author: author,
          permlink: permlink,
          max_accepted_payout: sbdaccept,
          percent_steem_dollars: sbdpercent,
          allow_votes: true,
          allow_curation_rewards: true,
          extensions: [
            [0, {
              beneficiaries: beneficiaries
            }]
          ]
        }]
      )
    }
    const result = await steem.broadcast.sendAsync(
      {
        operations,
        extensions: []
      },
      {
        posting: wifkey
      }
    )
    if (result) { // set status to published
      await con.query(
        'UPDATE `posts` SET `status`=1 WHERE `user`=? AND `permlink`=?',
        [author, permlink]
      )
      if (upvotepost === 1) {
        upvote(author, author, permlink, '10000')
      }
    } else { // set status to not published
      con.query(
        'UPDATE `posts` SET `status`=2 WHERE `user`=? AND `permlink`=?',
        [author, permlink]
      )
    }
  } catch (e) {
    con.query(
      'UPDATE `posts` SET `status`=2 WHERE `user`=? AND `permlink`=?',
      [author, permlink]
    )
  }
}

console.log('schedule Started.')
