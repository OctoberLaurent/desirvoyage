<?php

namespace App\Tests\Service;

use App\Service\AddressLookupService;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class AddressLookupServiceTest extends TestCase
{
    public function testSearchSkipsBlankQueriesWithoutCallingTheRemoteApi(): void
    {
        $client = $this->createMock(HttpClientInterface::class);
        $client->expects(self::never())->method('request');

        self::assertSame([], (new AddressLookupService($client))->search('   '));
    }

    public function testSearchUsesABoundedRequestAndReturnsTheDecodedPayload(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->expects(self::once())->method('toArray')->with(false)->willReturn(['features' => []]);
        $client = $this->createMock(HttpClientInterface::class);
        $client->expects(self::once())
            ->method('request')
            ->with('GET', 'https://api-adresse.data.gouv.fr/search/', [
                'query' => ['q' => '10 rue de la Paix'],
                'timeout' => 5.0,
            ])
            ->willReturn($response);

        self::assertSame(['features' => []], (new AddressLookupService($client))->search('10 rue de la Paix'));
    }
}
