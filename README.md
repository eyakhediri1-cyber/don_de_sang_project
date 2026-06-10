# Système de Gestion de Don de Sang

Application web de gestion d'un centre de collecte de sang, développée en PHP natif avec MySQL et Bootstrap 5.

---

## ✨ Fonctionnalités

| Module | Description | Rôles |
|--------|-------------|-------|
| **Authentification** | Sessions PHP, hachage bcrypt, autorisation par rôle | Tous |
| **Donneurs** | CRUD complet, recherche par groupe sanguin/ville, historique | Admin, Secrétaire |
| **Dons & Stock** | Enregistrement, validation médicale, traçabilité transfusions | Admin, Médecin, Secrétaire |
| **Tableau de bord** | Stats en temps réel, état du stock, alertes critiques | Tous |

---

## 🛠️ Stack Technique

- **Backend :** PHP 8.x natif (sans framework), PDO + requêtes préparées
- **Base de données :** MySQL/MariaDB — 7 tables (donneurs, dons, tests, transfusions, besoins…)
- **Frontend :** Bootstrap 5, responsive design
- **Sécurité :** Anti-injection SQL (PDO), anti-XSS (`htmlspecialchars`), mots de passe hachés

---

## 🗂️ Structure du Projet

```
bloodtrack/
├── config/         # Connexion PDO
├── auth/           # Login, logout, contrôle de session
├── donneurs/       # CRUD donneurs
├── dons/           # Gestion des dons et tests
├── transfusions/   # Traçabilité
├── dashboard/      # Tableau de bord & alertes
├── assets/         # Bootstrap, CSS custom
└── templates/      # Header, sidebar, footer partagés
```

---

## 🚀 Installation

```bash
# 1. Cloner le dépôt
git clone https://github.com/username/bloodtrack.git

# 2. Importer la base de données
mysql -u root -p < database/bloodtrack.sql

# 3. Configurer la connexion
cp config/config.example.php config/config.php
# → Renseigner DB_HOST, DB_NAME, DB_USER, DB_PASS

# 4. Lancer avec XAMPP/WAMP ou PHP built-in server
php -S localhost:8000
```

---

## 👥 Rôles Utilisateurs

| Rôle | Accès |
|------|-------|
| `ADMIN` | Accès complet + gestion utilisateurs/centres |
| `MEDECIN` | Validation des dons et saisie des résultats d'analyses |
| `SECRETAIRE` | Gestion des donneurs et enregistrement des dons |

---

## 📄 Licence

Projet académique — ISET Sousse, DSI2 · Atelier Développement Côté Serveur
