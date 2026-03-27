<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240101000001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create initial OptiFleet schema: utilisateur, categorie, vehicule, affectation, entretien, alerte';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<SQL
            CREATE TABLE categorie (
                id SERIAL PRIMARY KEY,
                libelle VARCHAR(100) NOT NULL UNIQUE,
                description TEXT DEFAULT NULL
            )
        SQL);

        $this->addSql(<<<SQL
            CREATE TABLE utilisateur (
                id SERIAL PRIMARY KEY,
                nom VARCHAR(100) NOT NULL,
                prenom VARCHAR(100) NOT NULL,
                email VARCHAR(255) NOT NULL UNIQUE,
                mot_de_passe VARCHAR(255) NOT NULL,
                role VARCHAR(50) NOT NULL DEFAULT 'ROLE_CONDUCTEUR'
                    CHECK (role IN ('ROLE_ADMIN','ROLE_GESTIONNAIRE','ROLE_CONDUCTEUR')),
                created_at TIMESTAMP NOT NULL DEFAULT NOW()
            )
        SQL);

        $this->addSql(<<<SQL
            CREATE TABLE vehicule (
                id SERIAL PRIMARY KEY,
                immatriculation VARCHAR(20) NOT NULL UNIQUE
                    CHECK (immatriculation ~ '^[A-Z]{2}-[0-9]{3}-[A-Z]{2}$'),
                marque VARCHAR(100) NOT NULL,
                modele VARCHAR(100) NOT NULL,
                annee SMALLINT NOT NULL,
                kilometrage INTEGER NOT NULL DEFAULT 0 CHECK (kilometrage >= 0),
                statut VARCHAR(20) NOT NULL DEFAULT 'disponible'
                    CHECK (statut IN ('disponible','en_mission','maintenance','inactif')),
                categorie_id INTEGER DEFAULT NULL REFERENCES categorie(id) ON DELETE SET NULL,
                latitude DECIMAL(10,7) DEFAULT NULL,
                longitude DECIMAL(10,7) DEFAULT NULL,
                adresse VARCHAR(255) DEFAULT NULL,
                created_at TIMESTAMP NOT NULL DEFAULT NOW(),
                updated_at TIMESTAMP NOT NULL DEFAULT NOW()
            )
        SQL);

        $this->addSql(<<<SQL
            CREATE TABLE affectation (
                id SERIAL PRIMARY KEY,
                date_debut TIMESTAMP NOT NULL,
                date_fin TIMESTAMP DEFAULT NULL,
                commentaire TEXT DEFAULT NULL,
                conducteur_id INTEGER NOT NULL REFERENCES utilisateur(id) ON DELETE CASCADE,
                vehicule_id INTEGER NOT NULL REFERENCES vehicule(id) ON DELETE CASCADE,
                created_at TIMESTAMP NOT NULL DEFAULT NOW()
            )
        SQL);

        $this->addSql(<<<SQL
            CREATE TABLE entretien (
                id SERIAL PRIMARY KEY,
                type VARCHAR(20) NOT NULL
                    CHECK (type IN ('revision','vidange','CT','freins','pneus','autre')),
                date_realise DATE NOT NULL,
                date_prochaine DATE DEFAULT NULL,
                km_prochaine INTEGER DEFAULT NULL CHECK (km_prochaine IS NULL OR km_prochaine >= 0),
                cout DECIMAL(10,2) DEFAULT NULL CHECK (cout IS NULL OR cout >= 0),
                notes TEXT DEFAULT NULL,
                vehicule_id INTEGER NOT NULL REFERENCES vehicule(id) ON DELETE CASCADE,
                created_at TIMESTAMP NOT NULL DEFAULT NOW()
            )
        SQL);

        $this->addSql(<<<SQL
            CREATE TABLE alerte (
                id SERIAL PRIMARY KEY,
                type VARCHAR(20) NOT NULL
                    CHECK (type IN ('assurance','CT','revision','vidange','autre')),
                message TEXT NOT NULL,
                date_echeance DATE NOT NULL,
                statut VARCHAR(20) NOT NULL DEFAULT 'en_attente'
                    CHECK (statut IN ('en_attente','en_cours','resolue')),
                vehicule_id INTEGER NOT NULL REFERENCES vehicule(id) ON DELETE CASCADE,
                created_at TIMESTAMP NOT NULL DEFAULT NOW()
            )
        SQL);

        // Indexes for performance
        $this->addSql('CREATE INDEX idx_vehicule_statut ON vehicule(statut)');
        $this->addSql('CREATE INDEX idx_vehicule_categorie ON vehicule(categorie_id)');
        $this->addSql('CREATE INDEX idx_affectation_vehicule ON affectation(vehicule_id)');
        $this->addSql('CREATE INDEX idx_affectation_conducteur ON affectation(conducteur_id)');
        $this->addSql('CREATE INDEX idx_entretien_vehicule ON entretien(vehicule_id)');
        $this->addSql('CREATE INDEX idx_entretien_date_prochaine ON entretien(date_prochaine)');
        $this->addSql('CREATE INDEX idx_alerte_vehicule ON alerte(vehicule_id)');
        $this->addSql('CREATE INDEX idx_alerte_statut ON alerte(statut)');
        $this->addSql('CREATE INDEX idx_alerte_date_echeance ON alerte(date_echeance)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS alerte');
        $this->addSql('DROP TABLE IF EXISTS entretien');
        $this->addSql('DROP TABLE IF EXISTS affectation');
        $this->addSql('DROP TABLE IF EXISTS vehicule');
        $this->addSql('DROP TABLE IF EXISTS utilisateur');
        $this->addSql('DROP TABLE IF EXISTS categorie');
    }
}
