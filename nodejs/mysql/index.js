// creating a MySQL pool which will handle a limited connections
// connections more than limitation will be added to the queue
const mysql = require('mysql')
const config = require('../config')
// changed from connection to the pool
const pool = mysql.createPool({
  // we are using this pool in the many files
  // assume we are using in the 10 files, so the actual limit will be (10 * connectionLimit)
  connectionLimit: process.env.DB_LIMIT || 40,
  host: config.db.host,
  user: config.db.user,
  password: config.db.pw,
  database: config.db.name,
  charset: 'utf8mb4'
})

// Rewriting MySQL query method as a promise
const con = {}
con.query = async (query, val) => {
  if (val) {
    let qu = await new Promise((resolve, reject) => {
      pool.query(query, val, (error, results) => {
        if (error) reject(new Error(error))
        resolve(results)
      })
    })
    return qu
  } else {
    let qu = await new Promise((resolve, reject) => {
      pool.query(query, (error, results) => {
        if (error) reject(new Error(error))
        resolve(results)
      })
    })
    return qu
  }
}

module.exports = con
