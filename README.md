# OptiFleet

Plateforme de gestion de flotte de véhicules d'entreprise.

## Description

OptiFleet permet aux entreprises de gérer leur parc automobile : suivi des véhicules, affectations aux conducteurs, entretiens, alertes automatiques et tableaux de bord KPIs.

## Stack technique

| Couche | Technologie |
|--------|-------------|
| Backend | Symfony 7 + API Platform 3 (PHP 8.3) |
| Frontend | React 18 + Vite + Tailwind CSS 3 |
| Base de données | PostgreSQL 16 + Doctrine ORM |
| Authentification | LexikJWT (RS256) |
| Infrastructure | Docker + docker-compose |
| CI/CD | GitHub Actions |
| Tests | PHPUnit (backend) + Vitest (frontend) |
| Graphiques | Recharts |
| State management | Zustand |

## Prérequis

- [Docker](https://www.docker.com/) >= 24
- [Docker Compose](https://docs.docker.com/compose/) >= 2
- Git

## Installation (3 commandes)

```bash
git clone https://github.com/Hakkai19z/OptiFleet.git
cp backend/.env.example backend/.env
docker-compose up -d
```

Les migrations sont appliquées automatiquement au démarrage.

## URLs d'accès

| Service | URL |
|---------|-----|
| Frontend | http://localhost:3000 |
| API Backend | http://localhost:8000/api |
| API Docs (Swagger) | http://localhost:8000/api/docs |

## Comptes de test

| Rôle | Email | Mot de passe |
|------|-------|--------------|
| Administrateur | admin@optifleet.fr | Admin1234! |
| Gestionnaire | gestionnaire@optifleet.fr | Admin1234! |
| Conducteur | conducteur@optifleet.fr | Admin1234! |

## Variables d'environnement obligatoires

Copier `backend/.env.example` vers `backend/.env` et renseigner :

```env
APP_SECRET=             # Clé secrète Symfony (32 chars min)
JWT_PASSPHRASE=         # Passphrase pour les clés JWT RSA
GOOGLE_MAPS_API_KEY=    # Clé API Google Maps Geocoding (optionnel)
MAILER_DSN=             # DSN SMTP pour les emails d'alertes
```

## Lancer les tests

```bash
# Tests backend (PHPUnit)
docker-compose exec app vendor/bin/phpunit --coverage-text

# Tests frontend (Vitest)
docker-compose exec front npm run test:coverage
```

## Architecture

```
optifleet/
├── backend/                    # Symfony 7 + API Platform
│   ├── src/
│   │   ├── Entity/             # Vehicule, Utilisateur, Entretien, Alerte, Affectation, Categorie
│   │   ├── Repository/         # Requêtes Doctrine personnalisées
│   │   ├── Service/            # VehiculeService, AlerteService, EntretienService, GeocodingService
│   │   ├── Controller/         # DashboardController, AuthController
│   │   ├── Security/           # VehiculeVoter, UtilisateurVoter
│   │   └── Scheduler/          # AlerteScheduler (cron 02:00 quotidien)
│   ├── migrations/             # Migrations Doctrine
│   ├── tests/                  # PHPUnit Unit + Functional
│   └── config/                 # Configuration Symfony
├── frontend/                   # React 18 + Vite
│   ├── src/
│   │   ├── components/
│   │   │   ├── ui/             # Button, Badge, Card, Input, Table, Toast, Skeleton
│   │   │   └── layout/         # Sidebar, TopBar, Layout
│   │   ├── pages/              # Login, Dashboard, Vehicules, Entretiens, Alertes, Admin
│   │   ├── hooks/              # useAuth, useVehicules, useAlertes
│   │   ├── services/           # api.js + services métier
│   │   └── store/              # Zustand (authStore, toastStore)
│   └── tests/                  # Vitest
├── .github/workflows/ci.yml    # CI GitHub Actions
├── docker-compose.yml          # Dev
└── docker-compose.prod.yml     # Production
```

## Entités principales

| Entité | Description |
|--------|-------------|
| **Vehicule** | Parc automobile (immatriculation, statut, kilométrage, géolocalisation) |
| **Utilisateur** | Admin / Gestionnaire / Conducteur (JWT, bcrypt cost 12) |
| **Affectation** | Lien véhicule ↔ conducteur avec vérification chevauchement |
| **Entretien** | Historique et planification (isEchu() par date ou km) |
| **Alerte** | Alertes automatiques générées par le scheduler |
| **Categorie** | Classification des véhicules |

## Fonctionnalités

- Authentification JWT RS256 (expiration 15 min, refresh 7 jours)
- Rate limiting : blocage après 5 tentatives échouées (15 min)
- CRUD complet véhicules avec validation immatriculation regex `AA-000-AA`
- Vérification chevauchement affectations
- Alertes automatiques (Symfony Scheduler, cron 02:00 quotidien)
- Emails d'alertes HTML (Symfony Mailer, template Twig)
- Dashboard KPIs (disponibilité, coûts maintenance 12 mois, graphiques)
- Géocodage d'adresses (Google Maps Geocoding API)
- Contrôle d'accès par rôles (Voters Symfony)
- Headers sécurité (X-Frame-Options, X-Content-Type-Options)
- Pages React avec squelettes de chargement, toasts, filtres, pagination

## Déploiement production

```bash
cp .env.example .env
# Renseigner toutes les variables de production
docker-compose -f docker-compose.prod.yml up -d
```

## Lancer les tests localement

```bash
# Backend
cd backend && vendor/bin/phpunit

# Frontend
cd frontend && npm run test
```

## Licence

MIT
