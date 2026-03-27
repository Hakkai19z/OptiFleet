<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuthTest extends WebTestCase
{
    public function testLoginSuccess(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/auth/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => 'admin@optifleet.fr',
                'motDePasse' => 'Admin1234!',
            ])
        );

        $this->assertResponseIsSuccessful();
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('token', $response);
        $this->assertNotEmpty($response['token']);
    }

    public function testLoginEchec(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/auth/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => 'admin@optifleet.fr',
                'motDePasse' => 'mauvais_mot_de_passe',
            ])
        );

        $this->assertResponseStatusCodeSame(401);
    }

    public function testLoginEmailInconnu(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/auth/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => 'inconnu@optifleet.fr',
                'motDePasse' => 'password',
            ])
        );

        $this->assertResponseStatusCodeSame(401);
    }

    public function testAccesApiSansToken(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/vehicules');
        $this->assertResponseStatusCodeSame(401);
    }

    public function testAccesApiAvecToken(): void
    {
        $client = static::createClient();

        // Login
        $client->request(
            'POST',
            '/api/auth/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => 'admin@optifleet.fr',
                'motDePasse' => 'Admin1234!',
            ])
        );

        $data = json_decode($client->getResponse()->getContent(), true);
        $token = $data['token'] ?? '';

        // Use token
        $client->request(
            'GET',
            '/api/vehicules',
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer '.$token]
        );

        $this->assertResponseIsSuccessful();
    }
}
