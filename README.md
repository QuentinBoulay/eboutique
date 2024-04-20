# Projet E-boutique Symfony

## Description
Notre "eboutique" est une application web développée avec Symfony, conçue pour fournir une solution complète pour les boutiques en ligne. Elle permet la gestion des produits, des catégories, des commandes, des utilisateurs, et des médias.

## Fonctionnalités
- Gestion des produits - OK
- Système de panier - OK
- Authentification et autorisation des utilisateurs - OK
- Interface administrateur pour la gestion des commandes - OK
- Gestion des catégories de produits - OK
- Gestion des adresses clients - OK
- Gestion des médias - OK

## Prérequis
- PHP 7.4 ou supérieur
- Composer
- Node.js pour la gestion des dépendances front-end
- Symfony

## Installation

1. Clonez le dépôt :
`git clone https://github.com/QuentinBoulay/eboutique.git`

2. Installez les dépendances PHP avec Composer :
`composer install`

3. Installez les dépendances npm :
`npm install`

4. Configurez votre fichier `.env` basé sur le `.env.test`.

5. Lancez les migrations pour créer la base de données :
`php bin/console doctrine:migrations:migrate`

6. Démarrez le serveur de développement :
`symfony server:start`

## Architecture de l'application

### Contrôleurs
Chaque contrôleur gère une fonction spécifique de l'application :
- **AdministrationController** : Gestion de l'interface d'administration de la boutique.
- **CartController** : Gère toutes les actions du panier d'achats, comme l'ajout et la suppression des produits.
- **CategoryController** : Permet la création, la modification, et la suppression des catégories de produits.
- **CustomerAddressController** : Gestion des adresses de livraison et de facturation des clients.
- **HomeController** : Affichage de la page d'accueil et gestion des vues principales.
- **MediaController** : Gestion des fichiers médias associés aux produits.
- **OrderController** : Traite les commandes, incluant le suivi et l'historique des commandes.
- **ProductController** : Gestion des produits, incluant l'ajout, la modification et la suppression.
- **RegistrationController** : Gestion de l'inscription des nouveaux utilisateurs.
- **SecurityController** : Gère l'authentification et les sessions des utilisateurs.
- **ShopController** : Affichage et gestion des vues liées à la boutique pour les utilisateurs.
- **UserController** : Gestion des profils utilisateurs.

### Entités
Les entités définissent la structure des données dans la base de données :
- **Category** : Détails des catégories de produits, comme le nom et la description.
- **CommandLine** : Peut-être utilisée pour les commandes via la ligne de commande ou pour stocker des détails spécifiques des commandes.
- **CustomerAddress** : Stocke les informations d'adresse pour les clients.
- **Media** : Contient des références aux fichiers médias tels que les images de produits.
- **Order** : Contient des informations sur les commandes des clients, y compris le statut et le paiement.
- **Product** : Stocke des informations sur les produits, comme le nom, le prix et la description.
- **User** : Gère les données des utilisateurs, incluant les informations d'authentification et de contact.

## Contribution

- Sébastien BURNET,
- Quentin BOULAY
