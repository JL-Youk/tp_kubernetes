<?php
$user = getenv('DB_USER') ?: '';
$pass = getenv('DB_PASS') ?: '';
$phrase = getenv('PHRASE') ?: '';

$response = [
    'user' => $user,
    'pass' => $pass,
    'PHRASE' => $phrase,
];

header('Content-Type: application/json');

echo json_encode($response, JSON_PRETTY_PRINT);
?>