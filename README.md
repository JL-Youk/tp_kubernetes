# TP Kubernetes

Ce projet est un TP utilisant un serveur PHP, une page Nginx et une base de données MySQL, le tout orchestré avec Kubernetes.

---

## 📦 Installation du projet via Git

### 1. Installer Git

Sur une machine Ubuntu/Debian, exécute la commande suivante :

```bash
sudo apt update
```
###  2. Installer Git:
```bash
sudo apt install git
```
### 2. Cloner le projet dans sa VM
```bash
git clone https://github.com/JL-Youk/tp_kubernetes.git
```
### 3. Ouvrir le projet
```bash
cd tp_kubernetes
```
### 4. Voir les mises à jour sur le projet 
```bash
git fetch
```
###  5. Récuperer les mises à jour du projet
```bash
git pull origin main
```


## 🔐 Paramètres de la base de données
Les accès à la base MySQL sont configurés comme suit :

Nom de la base : tweets

Utilisateur : tweetuser

Mot de passe utilisateur : tweetpass

Mot de passe root : rootpassword

Ces informations doivent être utilisées dans :

le script d'initialisation MySQL (init.sql) ( je vais l'ajouter )

le code PHP (index.php)

les manifestes Kubernetes (mysql-deployment.yaml, etc.)

## But du projet

### installer docker, kubernetes, minikube

tp_kubernetes/
├── backend-php/
│   └── index.php
│   └── Dockerfile
├── frontend/
│   └── index.html
│   └── Dockerfile
├── mysql/
│   └── init.sql
├── k8s/
│   └── backend-deployment.yaml
│   └── frontend-deployment.yaml
│   └── mysql-deployment.yaml
etc

### 2. Créer les Dockerfiles

### 3. Construire les images Docker

### 4. Lancer Minikube

### 5. Créer les fichiers YAML Kubernetes

### 6. Déployer sur le cluster

### 7. Vérifier le statut du cluster
```bash
kubectl get all
```
### 8. Accéder à l'application
```bash
minikube service frontend-service
```


