'use strict';
const fs = require('fs');
const path = require('path');
const { DatabaseSync } = require('node:sqlite');

const dbPath = path.resolve(__dirname, 'data.db');
const sqlPath = path.resolve(__dirname, 'sql', '02-Webnovels.sql');

try {
  const sql = fs.readFileSync(sqlPath, 'utf8');
  const db = new DatabaseSync(dbPath);
  db.exec(sql);
  console.log('Migration applied: Webnovels table created (if not exists).');
} catch (err) {
  console.error('Migration failed:', err.message || err);
  process.exit(1);
}
