-- Crée la base de données si elle n'existe pas
CREATE DATABASE IF NOT EXISTS tweets;

-- Utilise la base après création
USE tweets;

-- Crée l'utilisateur seulement s'il n'existe pas déjà
CREATE USER IF NOT EXISTS 'tweetuser'@'%' IDENTIFIED BY 'tweetpass';

-- Donne tous les droits à tweetuser sur la base tweets
GRANT ALL PRIVILEGES ON tweets.* TO 'tweetuser'@'%';

-- Applique les changements
FLUSH PRIVILEGES;

-- Crée la table de tweets si elle n'existe pas
CREATE TABLE IF NOT EXISTS tweets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    content VARCHAR(255) NOT NULL
);
