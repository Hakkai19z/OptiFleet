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
| Gestionnaire | gestionnaire@optifleet.fr | Gest1234! |
| Conducteur | conducteur@optifleet.fr | Cond1234! |

## Variables d'environnement obligatoires

Copier `backend/.env.example` vers `backend/.env` et renseigner :

```env
APP_SECRET=             # Clé secrète Symfony (32 chars min)
JWT_PASSPHRASE=         # Passphrase pour les clés JWT RSA
GOOGLE_MAPS_API_KEY=    # Clé API Google Maps Geocoding
MAILER_DSN=             # DSN SMTP pour les emails d'alertes
```

## Lancer les tests

```bash
# Tests backend (PHPUnit)
docker-compose exec app vendor/bin/phpunit --coverage-text

# Tests frontend (Vitest)
docker-compose exec front npm run test -- --coverage
```

## Architecture

```
optifleet/
├── backend/          # Symfony 7 + API Platform
├── frontend/         # React 18 + Vite
├── .github/
│   └── workflows/    # GitHub Actions CI
├── docker-compose.yml
└── docker-compose.prod.yml
```

## Entités principales

- **Vehicule** — Parc automobile avec statut et catégorie
- **Utilisateur** — Admin / Gestionnaire / Conducteur
- **Affectation** — Lien véhicule ↔ conducteur avec période
- **Entretien** — Historique et planification des maintenances
- **Alerte** — Alertes automatiques (assurance, CT, révision)
- **Categorie** — Classification des véhicules

## Fonctionnalités

- Authentification JWT (RS256) avec refresh token
- CRUD complet véhicules, entretiens, affectations
- Alertes automatiques (Symfony Scheduler, cron 24h)
- Emails d'alertes (Symfony Mailer, template HTML)
- Dashboard KPIs (disponibilité, coûts maintenance)
- Géocodage d'adresses (Google Maps API)
- Contrôle d'accès par rôles (Voters Symfony)
- Rate limiting (blocage après 5 tentatives échouées)

## Déploiement production

```bash
cp .env.example .env
# Renseigner les variables de production
docker-compose -f docker-compose.prod.yml up -d
```

## Licence

MIT
