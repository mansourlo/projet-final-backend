# ActuDynamic — Site d'actualité dynamique
## École Supérieure Polytechnique — Projet Final Backend

### Prérequis
- PHP >= 8.0 avec PDO et PDO_MySQL
- MySQL >= 5.7 ou MariaDB >= 10.3
- Serveur web (Apache/Nginx) ou `php -S localhost:8080`

---

### Installation

#### 1. Base de données
```sql
mysql -u root -p < base_de_donnees.sql
```

#### 2. Configuration
Éditer `config.php` et modifier les constantes :
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'actualites_db');
define('DB_USER', 'root');
define('DB_PASS', '');
```

#### 3. Lancer le serveur
```bash
cd projet/
php -S localhost:8080
# Ouvrir http://localhost:8080
```

---

### Comptes de démonstration
| Login | Mot de passe | Rôle |
|-------|-------------|------|
| admin | password | Administrateur |
| editeur1 | password | Éditeur |

> **Note :** Le mot de passe haché dans le SQL correspond à `password` (généré avec `PASSWORD_DEFAULT`).  
> Pour utiliser un autre mot de passe, régénérez le hash avec : `echo password_hash('votre_mdp', PASSWORD_DEFAULT);`

---

### Structure du projet
```
projet/
├── config.php              Configuration BDD + fonctions globales
├── entete.php              En-tête HTML
├── menu.php                Menu dynamique (selon rôle)
├── pied.php                Pied de page
├── accueil.php             Page d'accueil (liste + pagination + recherche)
├── connexion.php           Authentification
├── deconnexion.php         Déconnexion
├── index.php               Redirection vers accueil
├── base_de_donnees.sql     Script SQL complet
├── articles/
│   ├── detail.php          Affichage d'un article
│   ├── liste.php           Gestion des articles (éditeur)
│   ├── ajouter.php         Création d'article
│   ├── modifier.php        Modification d'article
│   └── supprimer.php       Suppression d'article
├── categories/
│   └── liste.php           CRUD catégories (éditeur)
├── utilisateurs/
│   ├── liste.php           Gestion utilisateurs (admin)
│   ├── ajouter.php         Création utilisateur
│   └── modifier.php        Modification utilisateur
└── assets/
    ├── css/style.css       Feuille de style principale
    ├── js/main.js          Interactions UI
    ├── js/validation.js    Validation formulaires (JS)
    └── uploads/            Images des articles (créé automatiquement)
```

---

### Fonctionnalités implémentées

#### Consultation publique (visiteurs)
- ✅ Page d'accueil avec liste des articles (titre, description, catégorie, date)
- ✅ Pagination Précédent / Suivant avec numérotation
- ✅ Détail complet d'un article
- ✅ Filtrage par catégorie
- ✅ **Bonus** : Recherche plein texte (titre, description, contenu)
- ✅ **Bonus** : Nombre d'articles par catégorie

#### Gestion (éditeurs)
- ✅ Ajouter / modifier / supprimer un article
- ✅ **Bonus** : Upload d'image pour les articles
- ✅ CRUD complet des catégories

#### Administration
- ✅ CRUD complet des utilisateurs
- ✅ Gestion des rôles (éditeur / administrateur)

#### Sécurité
- ✅ Sessions PHP avec `session_regenerate_id()`
- ✅ Protection de toutes les pages selon le rôle
- ✅ Requêtes préparées PDO (protection injection SQL)
- ✅ Échappement XSS via `htmlspecialchars()`
- ✅ Validation double : JavaScript (UX) + PHP (sécurité)
- ✅ Hachage des mots de passe avec `password_hash()` / `password_verify()`

---

### Technologies utilisées
- **Backend** : PHP 8+ avec PDO
- **Base de données** : MySQL / MariaDB
- **Frontend** : HTML5, CSS3 (variables CSS, Flexbox, Grid), JavaScript ES6
- **Polices** : Google Fonts (Inter)
