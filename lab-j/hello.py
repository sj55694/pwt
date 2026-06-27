import sys
import os

# Pobierz nazwę użytkownika (dla przykładu użyjemy "Jakub")
name = "Jakub"
# Numer indeksu
student_id = "55694"d

# Pobierz wersję Pythona
python_version = sys.version.split()[0]

# Pobierz ścieżkę do interpretera Pythona
python_location = sys.executable

# Wygeneruj napis używając f-string
print(f"Hello {name} ({student_id}). This environment is using Python version {python_version} at location {python_location}.")