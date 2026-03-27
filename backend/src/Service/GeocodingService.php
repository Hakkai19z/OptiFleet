<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class GeocodingService
{
    private const GEOCODING_URL = 'https://maps.googleapis.com/maps/api/geocode/json';

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly string $apiKey,
    ) {
    }

    public function geocode(string $adresse): ?array
    {
        if (empty($adresse) || empty($this->apiKey)) {
            return null;
        }

        try {
            $response = $this->httpClient->request('GET', self::GEOCODING_URL, [
                'query' => [
                    'address' => $adresse,
                    'key' => $this->apiKey,
                    'language' => 'fr',
                ],
            ]);

            $data = $response->toArray();

            if ($data['status'] !== 'OK' || empty($data['results'])) {
                return null;
            }

            $location = $data['results'][0]['geometry']['location'];

            return [
                'lat' => $location['lat'],
                'lng' => $location['lng'],
                'formatted_address' => $data['results'][0]['formatted_address'],
            ];
        } catch (\Throwable) {
            return null;
        }
    }
}
