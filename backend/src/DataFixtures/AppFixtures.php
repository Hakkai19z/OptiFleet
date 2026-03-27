<?php

namespace App\DataFixtures;

use App\Entity\Alerte;
use App\Entity\Categorie;
use App\Entity\Entretien;
use App\Entity\Utilisateur;
use App\Entity\Vehicule;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $hasher,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        // Catégories
        $categories = [];
        foreach (['Berline', 'Utilitaire', 'SUV', 'Citadine'] as $libelle) {
            $cat = new Categorie();
            $cat->setLibelle($libelle)->setDescription("Catégorie $libelle");
            $manager->persist($cat);
            $categories[] = $cat;
        }

        // Utilisateurs
        $admin = new Utilisateur();
        $admin->setNom('Mokhtari')
              ->setPrenom('Admin')
              ->setEmail('admin@optifleet.fr')
              ->setRole(Utilisateur::ROLE_ADMIN)
              ->setMotDePasse($this->hasher->hashPassword($admin, 'Admin1234!'));
        $manager->persist($admin);

        $gestionnaire = new Utilisateur();
        $gestionnaire->setNom('Martin')
                     ->setPrenom('Sophie')
                     ->setEmail('gestionnaire@optifleet.fr')
                     ->setRole(Utilisateur::ROLE_GESTIONNAIRE)
                     ->setMotDePasse($this->hasher->hashPassword($gestionnaire, 'Admin1234!'));
        $manager->persist($gestionnaire);

        $conducteur = new Utilisateur();
        $conducteur->setNom('Dupont')
                   ->setPrenom('Jean')
                   ->setEmail('conducteur@optifleet.fr')
                   ->setRole(Utilisateur::ROLE_CONDUCTEUR)
                   ->setMotDePasse($this->hasher->hashPassword($conducteur, 'Admin1234!'));
        $manager->persist($conducteur);

        // Véhicules
        $data = [
            ['AB-123-CD', 'Renault', 'Clio', 2021, 45000, 'disponible', 3],
            ['EF-456-GH', 'Peugeot', '308', 2022, 32000, 'disponible', 0],
            ['IJ-789-KL', 'Citroën', 'Berlingo', 2020, 87000, 'maintenance', 1],
            ['MN-012-OP', 'Volkswagen', 'Tiguan', 2023, 15000, 'disponible', 2],
        ];

        $vehicules = [];
        foreach ($data as [$immat, $marque, $modele, $annee, $km, $statut, $catIdx]) {
            $v = new Vehicule();
            $v->setImmatriculation($immat)
              ->setMarque($marque)
              ->setModele($modele)
              ->setAnnee($annee)
              ->setKilometrage($km)
              ->setStatut($statut)
              ->setCategorie($categories[$catIdx]);
            $manager->persist($v);
            $vehicules[] = $v;
        }

        // Entretien échu (pour déclencher une alerte)
        $entretien = new Entretien();
        $entretien->setType(Entretien::TYPE_REVISION)
                  ->setDateRealise(new \DateTime('-12 months'))
                  ->setDateProchaine(new \DateTime('-1 month'))
                  ->setCout('250.00')
                  ->setVehicule($vehicules[0]);
        $manager->persist($entretien);

        // Alerte exemple
        $alerte = new Alerte();
        $alerte->setType(Alerte::TYPE_REVISION)
               ->setMessage('Révision annuelle échue pour ' . $vehicules[0]->getImmatriculation())
               ->setDateEcheance(new \DateTime('-1 month'))
               ->setStatut(Alerte::STATUT_EN_ATTENTE)
               ->setVehicule($vehicules[0]);
        $manager->persist($alerte);

        $manager->flush();
    }
}
