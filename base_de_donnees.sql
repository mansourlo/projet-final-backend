-- ============================================
-- Script SQL - Site d'actualité dynamique
-- École Supérieure Polytechnique
-- ============================================

CREATE DATABASE IF NOT EXISTS actualites_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE actualites_db;

-- Table des catégories
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table des utilisateurs
CREATE TABLE IF NOT EXISTS utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    login VARCHAR(50) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    role ENUM('visiteur', 'editeur', 'administrateur') NOT NULL DEFAULT 'editeur',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table des articles
CREATE TABLE IF NOT EXISTS articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    description_courte TEXT NOT NULL,
    contenu LONGTEXT NOT NULL,
    categorie_id INT NOT NULL,
    auteur_id INT NOT NULL,
    image VARCHAR(255) DEFAULT NULL,
    date_publication DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (categorie_id) REFERENCES categories(id) ON DELETE CASCADE,
    FOREIGN KEY (auteur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- Données initiales
-- ============================================

-- Catégories
INSERT INTO categories (nom, description) VALUES
('Technologie', 'Actualités technologiques et innovations'),
('Sport', 'Résultats sportifs et compétitions'),
('Politique', 'Vie politique nationale et internationale'),
('Éducation', 'Actualités du monde éducatif'),
('Culture', 'Art, musique, cinéma et culture');

-- Administrateur par défaut (mot de passe : Admin1234!)
INSERT INTO utilisateurs (nom, prenom, login, mot_de_passe, role) VALUES
('Diallo', 'Amadou', 'admin', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77bvvy', 'administrateur'),
('Sow', 'Fatou', 'editeur1', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77bvvy', 'editeur');

-- Articles de démonstration
INSERT INTO articles (titre, description_courte, contenu, categorie_id, auteur_id) VALUES
('L\'intelligence artificielle révolutionne l\'éducation en Afrique', 'De nouvelles plateformes d\'IA transforment l\'accès à l\'éducation dans les pays africains.', 'L\'intelligence artificielle ouvre de nouvelles perspectives pour l\'éducation en Afrique. Des milliers d\'élèves bénéficient désormais de tuteurs virtuels disponibles 24h/24. Ces outils permettent un apprentissage personnalisé adapté au niveau de chaque étudiant. Des pays comme le Sénégal, le Kenya et le Rwanda sont à la pointe de cette révolution éducative numérique. Les gouvernements investissent massivement dans l\'infrastructure numérique pour démocratiser l\'accès au savoir.', 1, 2),
('CAN 2026 : Le Sénégal se qualifie brillamment', 'Les Lions de la Téranga s\'imposent 3-0 et valident leur ticket pour la phase finale.', 'Le Sénégal a réalisé une performance magistrale lors de la dernière journée des éliminatoires de la CAN 2026. Portés par un Sadio Mané en grande forme, les Lions de la Téranga ont dominé leur adversaire du début à la fin. L\'équipe nationale affiche une solidité défensive remarquable et une efficacité offensive redoutable. Le sélectionneur a salué l\'engagement de tous les joueurs. Le Sénégal se prépare désormais pour la phase finale avec ambition.', 2, 2),
('Réforme du système éducatif : nouvelles mesures annoncées', 'Le ministère de l\'Éducation présente un plan ambitieux de modernisation des programmes scolaires.', 'Le gouvernement a dévoilé un plan de réforme majeur du système éducatif national. Cette réforme vise à adapter les programmes aux exigences du marché du travail du 21ème siècle. L\'accent sera mis sur les sciences, la technologie, l\'ingénierie et les mathématiques. Des partenariats avec des universités étrangères sont prévus. La mise en œuvre progressive débutera dès la prochaine rentrée scolaire.', 4, 2),
('Festival culturel : célébration des arts et traditions locales', 'La 10ème édition du festival réunit artistes et artisans de toute la région.', 'Le festival culturel annuel a ouvert ses portes dans une ambiance festive exceptionnelle. Plus de 200 artistes et artisans participent à cet événement incontournable. Les visiteurs peuvent découvrir l\'artisanat local, la musique traditionnelle et la gastronomie régionale. Cette édition anniversaire rend hommage aux maîtres artisans qui ont perpétué les traditions. Le festival se poursuivra pendant une semaine avec des animations quotidiennes.', 5, 2),
('Sommet politique : discussions sur l\'intégration régionale', 'Les chefs d\'État de la région se réunissent pour renforcer la coopération économique.', 'Un sommet historique réunit les dirigeants de plusieurs pays pour discuter d\'une intégration régionale renforcée. Les discussions portent sur la libre circulation des personnes et des marchandises. Des accords commerciaux bilatéraux devraient être signés à l\'issue de ce sommet. La monnaie commune reste un sujet de débat au cœur des négociations. Les experts économiques saluent cette initiative qui pourrait booster le développement régional.', 3, 2);
