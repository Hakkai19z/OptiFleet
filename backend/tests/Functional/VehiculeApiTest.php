<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class VehiculeApiTest extends WebTestCase
{
    private function getToken(string $email, string $password): string
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/auth/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['email' => $email, 'motDePasse' => $password])
        );
        $data = json_decode($client->getResponse()->getContent(), true);
        return $data['token'] ?? '';
    }

    public function testGetVehiculesRequiresAuth(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/vehicules');
        $this->assertResponseStatusCodeSame(401);
    }

    public function testGetVehiculesWithAuth(): void
    {
        $client = static::createClient();
        $token = $this->getToken('admin@optifleet.fr', 'Admin1234!');

        $client->request(
            'GET',
            '/api/vehicules',
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer '.$token, 'CONTENT_TYPE' => 'application/json']
        );

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('hydra:member', $data);
    }

    public function testCreateVehiculeAsAdmin(): void
    {
        $client = static::createClient();
        $token = $this->getToken('admin@optifleet.fr', 'Admin1234!');

        $client->request(
            'POST',
            '/api/vehicules',
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer '.$token, 'CONTENT_TYPE' => 'application/json'],
            json_encode([
                'immatriculation' => 'TE-001-ST',
                'marque' => 'Test',
                'modele' => 'Auto',
                'annee' => 2024,
                'kilometrage' => 0,
                'statut' => 'disponible',
            ])
        );

        $this->assertResponseStatusCodeSame(201);
    }

    public function testCreateVehiculeAsConducteurForbidden(): void
    {
        $client = static::createClient();
        $token = $this->getToken('conducteur@optifleet.fr', 'Admin1234!');

        $client->request(
            'POST',
            '/api/vehicules',
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer '.$token, 'CONTENT_TYPE' => 'application/json'],
            json_encode([
                'immatriculation' => 'TE-002-ST',
                'marque' => 'Test',
                'modele' => 'Auto',
                'annee' => 2024,
            ])
        );

        $this->assertResponseStatusCodeSame(403);
    }

    public function testCreateVehiculeImmatriculationInvalide(): void
    {
        $client = static::createClient();
        $token = $this->getToken('admin@optifleet.fr', 'Admin1234!');

        $client->request(
            'POST',
            '/api/vehicules',
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer '.$token, 'CONTENT_TYPE' => 'application/json'],
            json_encode([
                'immatriculation' => 'invalide',
                'marque' => 'Test',
                'modele' => 'Auto',
                'annee' => 2024,
            ])
        );

        $this->assertResponseStatusCodeSame(422);
    }
}
