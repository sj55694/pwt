class Todo {
  constructor() {
    this.tasks = [];
    this.term = '';
    this.editingId = null;

    this.container = document.getElementById('todoList');
    this.searchInput = document.getElementById('searchInput');
    this.newTextInput = document.getElementById('newTaskText');
    this.newDeadlineInput = document.getElementById('newTaskDeadline');
    this.addButton = document.getElementById('addButton');

    this.loadFromLocalStorage();
    this.draw();

    this.searchInput.addEventListener('input', (e) => {
      this.term = e.target.value;
      this.draw();
    });

    this.addButton.addEventListener('click', () => this.handleAdd());

    document.addEventListener('click', (e) => {
      if (this.editingId !== null) {
        const editContainer = document.querySelector(`.todo-list li[data-id='${this.editingId}'] .edit-container`);
        if (editContainer && !editContainer.contains(e.target)) {
          this.finishEdit();
        }
      }
    });
  }

  loadFromLocalStorage() {
    const stored = localStorage.getItem('todoTasks');
    if (stored) {
      try {
        this.tasks = JSON.parse(stored);
      } catch {
        this.tasks = [];
      }
    } else {
      this.tasks = [];
    }
  }

  saveToLocalStorage() {
    localStorage.setItem('todoTasks', JSON.stringify(this.tasks));
  }

  addTask(text, deadline) {
    const trimmed = text.trim();
    if (trimmed.length < 3 || trimmed.length > 255) {
      alert('Tekst zadania musi mieć od 3 do 255 znaków.');
      return false;
    }

    if (deadline) {
      const deadlineDate = new Date(deadline);
      const now = new Date();
      if (deadlineDate <= now) {
        alert('Termin wykonania musi być w przyszłości (lub pusty).');
        return false;
      }
    }

    this.tasks.push({
      id: Date.now(),
      text: trimmed,
      deadline: deadline || null
    });
    this.saveToLocalStorage();
    this.draw();
    return true;
  }

  updateTask(id, newText, newDeadline) {
    const task = this.tasks.find(t => t.id === id);
    if (!task) return false;

    const trimmed = newText.trim();
    if (trimmed.length < 3 || trimmed.length > 255) {
      alert('Tekst zadania musi mieć od 3 do 255 znaków.');
      return false;
    }

    if (newDeadline) {
      const deadlineDate = new Date(newDeadline);
      const now = new Date();
      if (deadlineDate <= now) {
        alert('Termin wykonania musi być w przyszłości (lub pusty).');
        return false;
      }
    }

    task.text = trimmed;
    task.deadline = newDeadline || null;
    this.saveToLocalStorage();
    this.draw();
    return true;
  }

  deleteTask(id) {
    this.tasks = this.tasks.filter(task => task.id !== id);
    if (this.editingId === id) this.editingId = null;
    this.saveToLocalStorage();
    this.draw();
  }

  startEdit(id) {
    this.editingId = id;
    this.draw();
  }

  finishEdit() {
    if (this.editingId === null) return;

    const li = document.querySelector(`.todo-list li[data-id='${this.editingId}']`);
    if (!li) {
      this.editingId = null;
      this.draw();
      return;
    }

    const textInput = li.querySelector('.edit-input');
    const deadlineInput = li.querySelector('.edit-deadline');

    if (textInput && deadlineInput) {
      this.updateTask(this.editingId, textInput.value, deadlineInput.value);
    }

    this.editingId = null;
  }

  getFilteredTasks() {
    if (this.term.length >= 2) {
      const lowerTerm = this.term.toLowerCase();
      return this.tasks.filter(task => task.text.toLowerCase().includes(lowerTerm));
    }
    return [...this.tasks];
  }

  escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
  }

  highlightText(text) {
    if (this.term.length < 2) return this.escapeHtml(text);
    const lowerTerm = this.term.toLowerCase();
    const lowerText = text.toLowerCase();
    let index = lowerText.indexOf(lowerTerm);
    if (index === -1) return this.escapeHtml(text);
    const before = text.slice(0, index);
    const match = text.slice(index, index + this.term.length);
    const after = text.slice(index + this.term.length);
    return `${this.escapeHtml(before)}<mark>${this.escapeHtml(match)}</mark>${this.highlightText(after)}`;
  }

  draw() {
    if (!this.container) return;

    const filtered = this.getFilteredTasks();
    this.container.innerHTML = '';

    for (const task of filtered) {
      const li = document.createElement('li');
      li.setAttribute('data-id', task.id);

      if (this.editingId === task.id) {
        // Tryb edycji
        const editDiv = document.createElement('div');
        editDiv.className = 'edit-container';
        editDiv.style.display = 'flex';
        editDiv.style.gap = '10px';
        editDiv.style.alignItems = 'center';

        const textInput = document.createElement('input');
        textInput.type = 'text';
        textInput.value = task.text;
        textInput.className = 'edit-input';

        const deadlineInput = document.createElement('input');
        deadlineInput.type = 'datetime-local';
        deadlineInput.value = task.deadline || '';
        deadlineInput.className = 'edit-deadline';

        const saveButton = document.createElement('button');
        saveButton.textContent = 'Zapisz';
        saveButton.className = 'save-btn';

        editDiv.append(textInput, deadlineInput, saveButton);
        li.appendChild(editDiv);

        saveButton.addEventListener('click', (e) => {
          e.stopPropagation();
          this.finishEdit();
        });

        textInput.addEventListener('keypress', (e) => {
          if (e.key === 'Enter') {
            e.preventDefault();
            this.finishEdit();
          }
        });

        deadlineInput.addEventListener('keypress', (e) => {
          if (e.key === 'Enter') {
            e.preventDefault();
            this.finishEdit();
          }
        });
      } else {
        // Normalny widok
        const contentDiv = document.createElement('div');
        contentDiv.className = 'task-content';
        contentDiv.style.display = 'flex';
        contentDiv.style.justifyContent = 'space-between';
        contentDiv.style.alignItems = 'center';
        contentDiv.style.flex = '1';

        const textSpan = document.createElement('span');
        textSpan.className = 'task-text';
        textSpan.innerHTML = this.highlightText(task.text);

        const deadlineSpan = document.createElement('span');
        deadlineSpan.className = 'task-deadline';
        if (task.deadline) {
          const formatted = new Date(task.deadline).toLocaleString();
          deadlineSpan.textContent = `📅 ${formatted}`;
        } else {
          deadlineSpan.textContent = '📅 brak terminu';
        }

        contentDiv.append(textSpan, deadlineSpan);

        const deleteBtn = document.createElement('button');
        deleteBtn.textContent = 'Usuń';
        deleteBtn.className = 'delete-btn';

        li.append(contentDiv, deleteBtn);

        li.addEventListener('click', (e) => {
          if (e.target !== deleteBtn) {
            e.stopPropagation();
            this.startEdit(task.id);
          }
        });

        deleteBtn.addEventListener('click', (e) => {
          e.stopPropagation();
          this.deleteTask(task.id);
        });
      }

      this.container.appendChild(li);
    }
  }

  handleAdd() {
    const text = this.newTextInput.value;
    const deadline = this.newDeadlineInput.value;
    if (this.addTask(text, deadline)) {
      this.newTextInput.value = '';
      this.newDeadlineInput.value = '';
    }
  }
}

// Inicjalizacja po załadowaniu DOM
document.addEventListener('DOMContentLoaded', () => {
  window.todo = new Todo();
});
