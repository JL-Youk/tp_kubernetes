<?php
$host = getenv('DB_HOST') || '';
$db   = getenv('DB_NAME') || '';
$user = getenv('DB_USER') || '';
$pass = getenv('DB_PASS') || '';

$phrase = getenv('phrase') || '';

$response = [
    'phrase' => $phrase,
    'host' => $host,
    'db' => $db,
    'user' => $user,
    'pass' => $pass,
];

echo json_encode($response, JSON_PRETTY_PRINT);
?>