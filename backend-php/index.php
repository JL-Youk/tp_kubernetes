<?php
$user = getenv('DB_USER') ?: '';
$pass = getenv('DB_PASS') ?: '';
$phrase = getenv('phrase') ?: '';

$response = [
    'user' => $user,
    'pass' => $pass,
    'phrase' => $phrase,
];

header('Content-Type: application/json');

echo json_encode($response, JSON_PRETTY_PRINT);
?>