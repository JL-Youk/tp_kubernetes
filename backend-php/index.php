<?php
// Autoriser les requêtes depuis l'extérieur (CORS)
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Répondre rapidement aux pré-requêtes du navigateur (CORS)
    header("Access-Control-Allow-Methods: GET,POST,OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type");
    exit(0);
}
// Paramètres de connexion à la base de données (depuis les variables d'environnement)
$db_host = getenv('DB_HOST') ?: 'mysql';
$db_name = getenv('DB_NAME') ?: 'tweets';
$db_user = getenv('DB_USER') ?: 'root';
$db_pass = getenv('DB_PASS') ?: 'rootpassword';

// Connexion à MySQL
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Erreur de connexion à la base de données"]);
    exit();
}

// S'assurer que la table "tweets" existe (création si besoin)
$conn->query("CREATE TABLE IF NOT EXISTS tweets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    content VARCHAR(255) NOT NULL
)");



// Si c'est une requête POST, insérer le nouveau tweet
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer le contenu du tweet envoyé
    $tweetContent = isset($_POST['tweet']) ? $_POST['tweet'] : '';
    $tweetContent = trim($tweetContent);
    if ($tweetContent !== '') {
        // Échapper le contenu pour éviter les injections SQL
        $tweetEsc = $conn->real_escape_string($tweetContent);
        $conn->query("INSERT INTO tweets(content) VALUES ('$tweetEsc')");
    }
    // Pas de else: si le tweet est vide, on n'insère rien
}

// Récupérer la liste à jour des tweets
$result = $conn->query("SELECT content FROM tweets ORDER BY id ASC");
$tweets = [];
while ($row = $result->fetch_assoc()) {
    $tweets[] = $row['content'];
}

// Retourner la liste des tweets en JSON
echo json_encode($tweets);
?>