# API

## Sommaire
* [Architecture](#architecture)
  * [Technologies utilisées](#technologies-utilisees)
* [Installer le projet](#installer-le-projet)
  * [Récupérer les sources](#récupérer-les-sources)
  * [Fichier d'environnement](#fichier-denvironnement)
  * [Lancer la stack docker](#lancer-la-stack-docker)
  * [Déscription des services](#déscription-des-services)

## Architecture

### Technologies utilisées

L'API a été développée en PHP 7.4 et avec le framework Symfony 4.4.  
Nous avons utilisé certains component 

## Installer le projet
**Attention !**  
Vous devez avoir docker et docker-compose d'installer sur votre machine pour lancer l'API !  
L'API est par defaut lancée en mode production.

### Récupérer les sources
La première étape est de cloner le repository afin d'obtenir les sources du projet.  
Pour rappel :
```
SSH : git clone git@github.com:bref-n-share/api.git
HTTPS : git clone https://github.com/bref-n-share/api.git
```

### Fichier d'environnement

Le fichier d'environnement n'est pas versionné sur git pour des raisons de sécurité. Celui-ci vous a sûrement été fourni.  
Il faut remplacer le fichier existant (`data/www/.env.prod`) par celui qui vous a été fourni.  

### Lancer la stack docker
Nous avons décidé d'utiliser **docker compose** pour gérer les différents services de notre projet.  
Le fichier *docker-compose.yml* se trouvant à la racine du projet nous permet de mettre en place l'environnement de celui-ci.  

```
chemin_vers_le_projet/# docker-compose build  
chemin_vers_le_projet/# docker-compose up -d  
chemin_vers_le_projet/# docker exec -it api_fpm_1 /bin/bash  
root@fpm:/var/www/html# make  
```
Cette commande va lancer les 4 services liés à notre application, à savoir :
- db
- fpm
- nginx
- pgadmin

### Description des services
Nous avons créé un *Dockerfile* pour le service **fpm**. Les autres services quant à eux sont basés uniquement sur des images disponibles `https://hub.docker.com/`.  
Le *Dockerfile* et la configuration de **nginx** sont accessibles dans le dossier **services/**.

#### db
*Image: **postgres:12.1***  

Ce service correspond au container de la base de donnée. Nous avons décidé d'utiliser Postgres.  
Celle-ci écoute sur le port 5432. Vous pouvez visualiser les données grâce au service **pgadmin**.


#### fpm
*Lien vers le Dockerfile : **services/fpm/Dockerfile***  

Ce service est basé sur l'image *php:7.4-fpm*, nous avons ensuite ajouté composer, nos différentes configurations et les extentions php que nous utilisons, à savoir les extentions *iconv*, *gd*, *zip*, *pdo*, *pdo_pgsql*, *pgsql*.  

PHP-FPM permet la communication entre php et le serveur (**nginx**). Par defaut, **nginx** n'est pas capable d'interpreter.

Ce service écoute sur le port 9000 de votre machine. 

#### nginx

*Lien vers la configuration : **services/nginx/default.conf***  

Ce service est basé sur l'image *nginx:1.17*. Il correspond au serveur web.

Ce service écoute sur le port 80 de votre machine. 

#### pgadmin

Ce service est basé sur l'image *dpage/pgadmin4*. Il permet de visualiser les données.

Ce service écoute sur le port 8080 de votre machine.


## Connexion à PgAdmin
PgAdmin est accessible à l'adresse suivante : `http://localhost:8080/`

**Login**

> `Email :        tp@postgres.com`  
> `Mot de passe : tp`
