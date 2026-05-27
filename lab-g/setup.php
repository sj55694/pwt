<?php
$pdo = new PDO('sqlite:' . __DIR__ . '/data.db');

// Wczytaj i wykonaj skrypt SQL
$sql = file_get_contents(__DIR__ . '/sql/02-Webnovels.sql');
$pdo->exec($sql);

echo "Tabela Webnovels została utworzona!";
?>