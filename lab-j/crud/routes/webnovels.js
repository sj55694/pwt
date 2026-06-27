var express = require('express');
var router = express.Router();
const { DatabaseSync } = require('node:sqlite');
const path = require('path');
const dbPath = path.resolve(__dirname, '..', 'data.db');
let db;
try {
  db = new DatabaseSync(dbPath);
} catch (e) {
  console.error('Failed to open database:', e);
  db = null;
}

// List
router.get('/', function(req, res, next) {
  try {
    if (!db) throw new Error('Database not available');
    const rows = db.prepare('SELECT * FROM Webnovels').all();
    res.render('webnovels/index', { title: 'Webnovels List', webnovels: rows });
  } catch (err) {
    next(err);
  }
});

// Create form
router.get('/create', function(req, res) {
  res.render('webnovels/create', { title: 'Create Webnovel', webnovel: {} });
});

// Create action
router.post('/create', function(req, res, next) {
  try {
    if (!db) throw new Error('Database not available');
    const { Tytul, Autor, Opis } = req.body || {};
    const stmt = db.prepare('INSERT INTO Webnovels (Tytul, Autor, Opis) VALUES (?,?,?)');
    stmt.run(Tytul || '', Autor || '', Opis || '');
    res.redirect('/webnovels');
  } catch (err) {
    next(err);
  }
});

// Show
router.get('/:id', function(req, res, next) {
  try {
    if (!db) throw new Error('Database not available');
    const id = req.params.id;
    const row = db.prepare('SELECT * FROM Webnovels WHERE id = ?').get(Number(id));
    if (!row) return res.status(404).send('Not found');
    res.render('webnovels/show', { title: row.Tytul, webnovel: row });
  } catch (err) {
    next(err);
  }
});

// Edit form
router.get('/:id/edit', function(req, res, next) {
  try {
    if (!db) throw new Error('Database not available');
    const id = req.params.id;
    const row = db.prepare('SELECT * FROM Webnovels WHERE id = ?').get(Number(id));
    if (!row) return res.status(404).send('Not found');
    res.render('webnovels/edit', { title: 'Edit Webnovel', webnovel: row });
  } catch (err) {
    next(err);
  }
});

// Edit action
router.post('/:id/edit', function(req, res, next) {
  try {
    if (!db) throw new Error('Database not available');
    const id = req.params.id;
    const { Tytul, Autor, Opis } = req.body || {};
    const stmt = db.prepare('UPDATE Webnovels SET Tytul = ?, Autor = ?, Opis = ? WHERE id = ?');
    stmt.run(Tytul || '', Autor || '', Opis || '', Number(id));
    res.redirect('/webnovels');
  } catch (err) {
    next(err);
  }
});

// Delete (GET for simplicity)
router.get('/:id/delete', function(req, res, next) {
  try {
    if (!db) throw new Error('Database not available');
    const id = req.params.id;
    const stmt = db.prepare('DELETE FROM Webnovels WHERE id = ?');
    stmt.run(Number(id));
    res.redirect('/webnovels');
  } catch (err) {
    next(err);
  }
});

module.exports = router;
