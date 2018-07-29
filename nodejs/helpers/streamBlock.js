const call = require('./nodeCall')
const config = require('../config')

const streamBlockNumber = async (cb) => {
  let lastBlock = 0
  setInterval(async () => {
    const result = await call(
      config.steemd,
      'condenser_api.get_dynamic_global_properties',
      []
    )
    if (result && result.head_block_number && !isNaN(result.head_block_number)) {
      if (result.head_block_number > lastBlock) {
        lastBlock = result.head_block_number
        cb(lastBlock)
      }
    }
  }, 500)
}

const streamBlockOperations = async (cb) => {
  streamBlockNumber(async blockNumber => {
    const result = await call(
      config.steemd,
      'condenser_api.get_block',
      [blockNumber]
    )
    if (result) {
      const operations = result.transactions.map(transaction => {
        return transaction.operations
      })
      if (operations.length > 0) {
        for (let operation of operations) {
          cb(operation)
        }
      }
    }
  })
}

module.exports = {
  streamBlockNumber,
  streamBlockOperations
}
