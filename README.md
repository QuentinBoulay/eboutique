# Projet E-boutique Symfony

## Description
Notre "eboutique" est une application web développée avec Symfony, conçue pour fournir une solution complète pour les boutiques en ligne. Elle permet la gestion des produits, des catégories, des commandes, des utilisateurs, et des médias.

## Fonctionnalités
- Gestion des produits
- Système de panier
- Authentification et autorisation des utilisateurs
- Interface administrateur pour la gestion des commandes
- Gestion des catégories de produits
- Gestion des adresses clients
- Gestion des médias

## Prérequis
- PHP 7.4 ou supérieur
- Composer
- Node.js pour la gestion des dépendances front-end
- Symfony

## Installation

1. Clonez le dépôt :
`git clone [[url-du-dépôt]](https://github.com/QuentinBoulay/eboutique.git)`

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

### Repositories
Chaque repository facilite l'interaction avec la base de données pour une entité spécifique :
- **CategoryRepository** : Opérations liées aux catégories de produits.
- **CommandLineRepository** : Gestion des lignes de commandes des commandes.
- **CustomerAddressRepository** : Manipulation des adresses clients.
- **MediaRepository** : Gestion des fichiers médias liés aux produits.
- **OrderRepository** : Central pour le traitement des commandes.
- **ProductRepository** : Essentiel pour la gestion du catalogue de produits.
- **UserRepository** : Traite toutes les opérations liées aux utilisateurs.

## Contribution

- Sébastien BURNET,
- Quentin BOULAY