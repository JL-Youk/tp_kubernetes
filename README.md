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

```bash
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
│   └── api-deployment.yaml
│   └── frontend-deployment.yaml
│   └── mysql-deployment.yaml
    etc
```

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





# Explication pour créer le service api

## Installation
### Avoir docker, minikube et kubernetes sur sa machine
### verifier avec les commandes:

```bash
docker --version
```
```bash
minikube version
```
```bash
kubectl version --client
```

## Démarrer le cluster Minikube
### Lancez Minikube
```bash
minikube start --driver=docker
```
Minikube va télécharger les images nécessaires et initialiser un cluster Kubernetes un nœud. Ce processus peut prendre quelques minutes la première fois. Une fois terminé, vous devriez voir un message confirmant que le cluster est démarré et configuré, par exemple :
### Vous pouvez tester en listant le nœud Kubernetes
```bash
kubectl get nodes
```
### Sortie attendue :
```bash
NAME       STATUS   ROLES           AGE   VERSION
minikube   Ready    control-plane   1m    v1.xx.x
```

##  Dockerfile pour l’API PHP
### Créons le fichier backend-php/Dockerfile avec le contenu suivant :
```bash
# Utiliser l'image PHP officielle avec Apache
FROM php:8.1-apache

# Installer l'extension PHP MySQLi (et PDO) pour permettre la connexion à MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copie du code de l'API (index.php) dans le répertoire par défaut d'Apache (/var/www/html)
COPY index.php /var/www/html/

# Exposer le port 80 (optionnel, pour documentation)
EXPOSE 80
```

### Construction et chargement des images Docker dans Minikube
```bash
eval $(minikube docker-env)
```
Cette commande modifie vos variables d’environnement Docker (DOCKER_HOST, etc.) pour pointer vers le Docker de Minikube. Désormais, toute commande docker build ou docker images agit à l’intérieur de Minikube. (Pour revenir au Docker local par défaut, utilisez eval $(minikube docker-env -u)).
```

### Construisons les images (assurez-vous d’être dans le répertoire tp-minikube où se trouvent les dossiers ou sinon adapté la commande suivante):

```bash
docker build -t php-api:1.0 ./backend-php
```

Ces commandes vont lancer la construction des images en utilisant les Dockerfile de chaque dossier. Une connexion internet est requise au moins la première fois pour télécharger les images de base (php:8.1-apache et nginx:1.23-alpine). Une fois terminées, vous pouvez vérifier que les images sont bien présentes dans Docker (toujours dans l’environnement Minikube) :
```bash
docker images
```

## Création des manifestes Kubernetes (YAML) pour le déploiement

### Déploiement et Service pour l’API PHP
#### Déploiement
```bash
apiVersion: apps/v1
kind: Deployment
metadata:
  name: api
spec:
  replicas: 1
  selector:
    matchLabels:
      app: api
  template:
    metadata:
      labels:
        app: api
    spec:
      containers:
      - name: php-api
        image: php-api:1.0         # image construite pour l'API PHP
        imagePullPolicy: IfNotPresent
```
#### Service
```bash
apiVersion: v1
kind: Service
metadata:
  name: api-service
spec:
  selector:
    app: api
  ports:
    - port: 80
      targetPort: 80
      nodePort: 30081
  type: NodePort
```
### Explications
#### Le **Deployment api** crée un pod pour l’API PHP à partir de notre image locale `php-api:1.0`. Kubernetes va chercher cette image via le daemon Docker de Minikube (c’est pour cela qu’on a construit l’image dans Minikube). Le `imagePullPolicy: IfNotPresent` évite de tenter de la télécharger d'un registre.

#### Le **Service api-service** expose l’API PHP. Nous choisissons ici de le faire via un **NodePort** (port accessible depuis l’extérieur du cluster sur le nœud). On fixe `nodePort: 30081` (un nombre dans la plage 30000-32767) pour connaître à l’avance le port à utiliser. Ce service écoutera le port 80 du côté du cluster (c’est-à-dire toute requête arrivant sur `api-service:80` sera routée vers le conteneur sur son port 80). Avec `NodePort`, Kubernetes ouvre sur le nœud (ici la VM Minikube) le port 30081 et toute connexion reçue sur ce port sera redirigée vers le service. Cela nous permettra d’appeler l’API depuis le navigateur (via l’IP de Minikube et ce port).
    
#### Le selector `app: api` lie ce service au pod/deployment de l’API.


## API (Deployment + Service)
```bash
kubectl apply -f k8s/api-deployment.yaml
kubectl apply -f k8s/api-service.yaml
```

### Une fois tout appliqué, vérifiez l’état des Pods et Services :
```bash
kubectl get pods
```
#### Vous devriez voir trois pods (nommés avec les préfixes ( ou alors uniquement api-... si vous n'avez pas créer les autres pods ) `mysql-...`, `api-...`, `frontend-...`) avec **STATUS** `Running`. Au début, le pod MySQL peut prendre quelques instants pour initialiser la base de données (STATUS `ContainerCreating` puis `Running`). Le pod API peut ne pas être prêt tant que MySQL n’est pas opérationnel (selon comment Apache/PHP gère la connexion, mais dans notre cas il va simplement renvoyer une erreur s’il n’arrive pas à se connecter, puis ça fonctionnera aux essais suivants). Le pod frontend devrait démarrer très rapidement.

### Vous pouvez aussi lister les services pour voir les NodePorts :
```bash
kubectl get svc
```

#### Info❗Si tu ne faisais pas eval $(minikube docker-env) et construisais ton image en local (sur ton hôte), alors Kubernetes dans Minikube ne verrait pas l'image.
Dans ce cas, tu devrais pousser l'image dans un registre (ex: Docker Hub ou un registre privé) et indiquer son nom complet :
```bash
image: mydockerhubuser/php-api:1.0
```


## Creation du fichier secret.yaml
Les secrets permettent de stocker des informations sensibles (comme les mots de passe) de manière sécurisée dans Kubernetes.

```bash
apiVersion: v1
kind: Secret
metadata:
  name: api-secret
type: Opaque
data:
  mysql-user: dHdlZXR1c2Vy
  mysql-password: dHdlZXRwYXNz
```
### Appliquer le Secret :
```bash
kubectl apply -f k8s/secret.yaml
```

## Création du fichier configMap.yaml

Les ConfigMap permettent de passer des configurations non sensibles (noms de base de données, utilisateurs, etc.).
```bash
apiVersion: v1
kind: ConfigMap
metadata:
  name: api-config
data:
  phrase: Hello
```
### Appliquer le ConfigMap :
```bash
kubectl apply -f k8s/configmap.yaml
```

## Utilisation dans le déploiement.yaml

```bash
spec:
  containers:
  - name: php-api
    image: php-api:1.0         # image construite pour l'API PHP
    imagePullPolicy: IfNotPresent
    env:
    - name: DB_USER
      valueFrom:
        secretKeyRef:
          name: api-secret
          key: mysql-user
    - name: DB_PASS
      valueFrom:
        secretKeyRef:
          name: api-secret
          key: mysql-password
    - name: PHRASE
      valueFrom:
        configMapKeyRef:
          name: api-config
          key: phrase
```
### 

## Création du fichier hpa.yaml (autoscaling)
```bash
apiVersion: autoscaling/v2
kind: HorizontalPodAutoscaler
metadata:
  name: api-hpa
spec:
  scaleTargetRef:
    apiVersion: apps/v1
    kind: Deployment
    name: api
  minReplicas: 3
  maxReplicas: 20
  metrics:
  - type: Resource
    resource:
      name: cpu
      target:
        type: Utilization
        averageUtilization: 60
```
Ce HPA maintient 3 à 20 pods selon l'utilisation CPU, et scale lorsque la moyenne dépasse 60%.

### Appliquer le HPA :
```bash
kubectl apply -f k8s/hpa.yaml
```

## Vérification finale
### Liste les pods, services, HPA
```bash
kubectl get all
kubectl get hpa
```
### Voir l’état du HPA :
```bash
kubectl describe hpa api-hpa
```