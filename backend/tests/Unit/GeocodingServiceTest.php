<?php

namespace App\Tests\Unit;

use App\Service\GeocodingService;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class GeocodingServiceTest extends TestCase
{
    public function testGeocodeRetourneCoordonnees(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->method('toArray')->willReturn([
            'status' => 'OK',
            'results' => [
                [
                    'geometry' => ['location' => ['lat' => 48.8697, 'lng' => 2.3308]],
                    'formatted_address' => '1 Rue de la Paix, 75001 Paris, France',
                ],
            ],
        ]);

        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->method('request')->willReturn($response);

        $service = new GeocodingService($httpClient, 'fake-api-key');
        $result = $service->geocode('1 Rue de la Paix, Paris');

        $this->assertNotNull($result);
        $this->assertSame(48.8697, $result['lat']);
        $this->assertSame(2.3308, $result['lng']);
    }

    public function testGeocodeRetourneNullSiStatusErreur(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->method('toArray')->willReturn(['status' => 'ZERO_RESULTS', 'results' => []]);

        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->method('request')->willReturn($response);

        $service = new GeocodingService($httpClient, 'fake-api-key');
        $result = $service->geocode('adresse introuvable xyz');

        $this->assertNull($result);
    }

    public function testGeocodeRetourneNullSiAdresseVide(): void
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->expects($this->never())->method('request');

        $service = new GeocodingService($httpClient, 'fake-api-key');
        $result = $service->geocode('');

        $this->assertNull($result);
    }

    public function testGeocodeRetourneNullSiApiKeyVide(): void
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->expects($this->never())->method('request');

        $service = new GeocodingService($httpClient, '');
        $result = $service->geocode('Paris');

        $this->assertNull($result);
    }

    public function testGeocodeGereExceptionReseau(): void
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->method('request')->willThrowException(new \RuntimeException('Network error'));

        $service = new GeocodingService($httpClient, 'fake-api-key');
        $result = $service->geocode('Paris');

        $this->assertNull($result);
    }
}
