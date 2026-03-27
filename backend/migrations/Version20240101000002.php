<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240101000002 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Seed default admin, gestionnaire and conducteur accounts';
    }

    public function up(Schema $schema): void
    {
        // Passwords are bcrypt cost 12:
        // Admin1234! => $2y$12$...
        // Gest1234!  => $2y$12$...
        // Cond1234!  => $2y$12$...
        $this->addSql(<<<SQL
            INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, role) VALUES
            ('Mokhtari', 'Admin', 'admin@optifleet.fr',
             '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
             'ROLE_ADMIN'),
            ('Martin', 'Sophie', 'gestionnaire@optifleet.fr',
             '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
             'ROLE_GESTIONNAIRE'),
            ('Dupont', 'Jean', 'conducteur@optifleet.fr',
             '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
             'ROLE_CONDUCTEUR')
        SQL);

        $this->addSql(<<<SQL
            INSERT INTO categorie (libelle, description) VALUES
            ('Berline', 'Voitures berlines de représentation'),
            ('Utilitaire', 'Véhicules utilitaires et fourgons'),
            ('SUV', 'Véhicules SUV et 4x4'),
            ('Citadine', 'Petites voitures de ville')
        SQL);

        $this->addSql(<<<SQL
            INSERT INTO vehicule (immatriculation, marque, modele, annee, kilometrage, statut, categorie_id) VALUES
            ('AB-123-CD', 'Renault', 'Clio', 2021, 45000, 'disponible', 4),
            ('EF-456-GH', 'Peugeot', '308', 2022, 32000, 'disponible', 1),
            ('IJ-789-KL', 'Citroën', 'Berlingo', 2020, 87000, 'maintenance', 2),
            ('MN-012-OP', 'Volkswagen', 'Tiguan', 2023, 15000, 'disponible', 3)
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DELETE FROM vehicule WHERE immatriculation IN ('AB-123-CD','EF-456-GH','IJ-789-KL','MN-012-OP')");
        $this->addSql("DELETE FROM categorie WHERE libelle IN ('Berline','Utilitaire','SUV','Citadine')");
        $this->addSql("DELETE FROM utilisateur WHERE email IN ('admin@optifleet.fr','gestionnaire@optifleet.fr','conducteur@optifleet.fr')");
    }
}
