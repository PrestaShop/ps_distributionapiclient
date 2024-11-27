<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

declare(strict_types=1);

namespace PrestaShop\Module\DistributionApiClient\Tests\Middleware;

use Doctrine\Common\Cache\CacheProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\Module\DistributionApiClient\Middleware\CachedHttpClient;
use PrestaShop\Module\DistributionApiClient\Middleware\CachedResponse;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class CachedHttpClientTest extends TestCase
{
    /** @var CacheProvider&MockObject */
    private $cacheProvider;

    public function setUp(): void
    {
        $this->cacheProvider = $this->getMockBuilder(CacheProvider::class)->getMock();
    }

    public function testGetValidResponse(): void
    {
        $this->cacheProvider->expects($this->once())->method('contains');
        $this->cacheProvider->expects($this->once())->method('save');
        $this->cacheProvider->expects($this->never())->method('fetch');

        $response = new MockResponse('content');
        $httpClient = new MockHttpClient($response);

        $this->cacheProvider->method('save')->willReturnCallback(function ($id, $response) {
            $this->assertInstanceOf(CachedResponse::class, $response);
        });

        $cachedClient = new CachedHttpClient($this->cacheProvider, [], $httpClient);
        $cachedResponse = $cachedClient->request('GET', '/');
        $this->assertInstanceOf(CachedResponse::class, $cachedResponse);
        $this->assertEquals('content', $cachedResponse->getContent());
    }

    public function testInvalidResponse(): void
    {
        $this->cacheProvider->expects($this->once())->method('contains');
        $this->cacheProvider->expects($this->never())->method('save');
        $this->cacheProvider->expects($this->never())->method('fetch');

        $response = new MockResponse('error content', ['http_code' => 500]);
        $httpClient = new MockHttpClient($response);

        $cachedClient = new CachedHttpClient($this->cacheProvider, [], $httpClient);
        $notCachedResponse = $cachedClient->request('GET', '/');
        $this->assertInstanceOf(MockResponse::class, $notCachedResponse);
        $this->assertEquals('error content', $notCachedResponse->getContent(false));
    }

    public function testValidResponseFromCache(): void
    {
        $mockResponse = $this->getMockBuilder(ResponseInterface::class)->getMock();
        $mockResponse->expects($this->once())->method('getStatusCode')->willReturn(200);
        $mockResponse->expects($this->once())->method('getContent')->willReturn('content');
        $mockResponse->expects($this->once())->method('getHeaders')->willReturn(['http_code' => 200]);
        $mockResponse->expects($this->once())->method('getInfo')->willReturn(['http_code' => 200]);
        $mockCachedResponse = new CachedResponse($mockResponse);

        $this->cacheProvider->method('contains')->willReturn(true);
        $this->cacheProvider->expects($this->once())->method('fetch')->willReturn($mockCachedResponse);
        $this->cacheProvider->expects($this->never())->method('save');

        // HTTP client should never be used since the response is returned by the cache
        $httpClient = $this->getMockBuilder(HttpClientInterface::class)->getMock();
        $httpClient->expects($this->never())->method('request');

        $cachedClient = new CachedHttpClient($this->cacheProvider, [], $httpClient);
        $cachedResponse = $cachedClient->request('GET', '/');
        $this->assertInstanceOf(CachedResponse::class, $cachedResponse);
        $this->assertEquals('content', $cachedResponse->getContent());
    }
}
