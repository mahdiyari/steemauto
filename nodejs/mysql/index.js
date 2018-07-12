// creating a MySQL pool which will handle up to 1,000 connections
// connections more than 1,000 will be added to the queue
const mysql = require('mysql')
const config = require('../config')
// changed from connection to the pool
const pool = mysql.createPool({
  connectionLimit: 1000,
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
