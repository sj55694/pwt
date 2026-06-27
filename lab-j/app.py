from flask import Flask, render_template, request, redirect, url_for, g, abort
import sqlite3
import os

DB_PATH = os.path.join(os.path.dirname(__file__), 'webnovels.db')

app = Flask(__name__)
app.config['SECRET_KEY'] = 'dev'


def ensure_db():
    conn = sqlite3.connect(DB_PATH)
    conn.execute(
        """
        CREATE TABLE IF NOT EXISTS Webnovels (
            id integer primary key autoincrement,
            Tytul text not null,
            Autor text not null,
            Opis text not null
        )
        """
    )
    conn.commit()
    conn.close()



def get_db():
    db = getattr(g, '_database', None)
    if db is None:
        db = g._database = sqlite3.connect(DB_PATH)
        db.row_factory = sqlite3.Row
    return db


@app.teardown_appcontext
def close_connection(exception):
    db = getattr(g, '_database', None)
    if db is not None:
        db.close()


@app.route('/')
def index():
    return render_template('index.html', title='Home')


@app.route('/webnovels')
def webnovels_index():
    db = get_db()
    cur = db.execute('SELECT id, Tytul, Autor FROM Webnovels ORDER BY id DESC')
    webnovels = cur.fetchall()
    return render_template('webnovels_index.html', title='Webnovels', webnovels=webnovels)


@app.route('/webnovels/create', methods=('GET', 'POST'))
def webnovels_create():
    if request.method == 'POST':
        Tytul = request.form.get('Tytul', '').strip()
        Autor = request.form.get('Autor', '').strip()
        Opis = request.form.get('Opis', '').strip()
        db = get_db()
        db.execute('INSERT INTO Webnovels (Tytul, Autor, Opis) VALUES (?, ?, ?)', (Tytul, Autor, Opis))
        db.commit()
        return redirect(url_for('webnovels_index'))

    webnovel = {'Tytul':'', 'Autor':'', 'Opis':''}
    return render_template('webnovels_create.html', title='Create Webnovel', webnovel=webnovel)


@app.route('/webnovels/<int:item_id>')
def webnovels_show(item_id):
    db = get_db()
    cur = db.execute('SELECT * FROM Webnovels WHERE id = ?', (item_id,))
    row = cur.fetchone()
    if row is None:
        abort(404)
    return render_template('webnovels_show.html', title=row['Tytul'], webnovel=row)


@app.route('/webnovels/<int:item_id>/edit', methods=('GET', 'POST'))
def webnovels_edit(item_id):
    db = get_db()
    cur = db.execute('SELECT * FROM Webnovels WHERE id = ?', (item_id,))
    row = cur.fetchone()
    if row is None:
        abort(404)

    if request.method == 'POST':
        Tytul = request.form.get('Tytul', '').strip()
        Autor = request.form.get('Autor', '').strip()
        Opis = request.form.get('Opis', '').strip()
        db.execute('UPDATE Webnovels SET Tytul = ?, Autor = ?, Opis = ? WHERE id = ?', (Tytul, Autor, Opis, item_id))
        db.commit()
        return redirect(url_for('webnovels_show', item_id=item_id))

    return render_template('webnovels_edit.html', title='Edit Webnovel', webnovel=row)


@app.route('/webnovels/<int:item_id>/delete')
def webnovels_delete(item_id):
    db = get_db()
    db.execute('DELETE FROM Webnovels WHERE id = ?', (item_id,))
    db.commit()
    return redirect(url_for('webnovels_index'))


if __name__ == '__main__':
    # ensure database and tables exist
    ensure_db()
    # Run on port required by the lab
    app.run(host='0.0.0.0', port=55694, debug=True)
