const fetch = require('node-fetch')
// This method will send jsonrpc 2.0 requests to the appbase(now v0.19.10) nodes
// This method will work with old rpc nodes, but needs to send right methods
// This method will ignore any error! we can just check !result in the response
const call = async (steemd, method, params) => {
  try {
    const body = JSON.stringify({
      id: 0,
      jsonrpc: '2.0',
      method,
      params
    })
    const res = await fetch(
      steemd,
      {
        method: 'POST',
        body
      }
    )
    if (res.ok) {
      const result = await res.json()
      return result.result
    } else {
      return null
    }
  } catch (e) {
    return null
  }
}

module.exports = call
