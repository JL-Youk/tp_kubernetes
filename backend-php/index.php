<?php
// Récupération des variables d’environnement
$host = getenv('DB_HOST') || '';
$db   = getenv('DB_NAME') || '';
$user = getenv('DB_USER') || '';
$pass = getenv('DB_PASS') || '';
$phrase_acceuil = getenv('phrase_acceuil') || '';

$response = [
    'phrase_acceuil' => $phrase_acceuil,
    'host' => $host,
    'db' => $db,
    'user' => $user,
    'pass' => $pass,
];

echo json_encode($response, JSON_PRETTY_PRINT);
?>