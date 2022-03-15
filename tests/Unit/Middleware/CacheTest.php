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

use Closure;
use Doctrine\Common\Cache\CacheProvider;
use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\Module\DistributionApiClient\Middleware\Cache;
use PrestaShop\Module\DistributionApiClient\Middleware\CachedResponse;

class CacheTest extends TestCase
{
    /** @var Cache */
    private $cache;

    /** @var CacheProvider&MockObject */
    private $cacheProvider;

    public function setUp(): void
    {
        $this->cacheProvider = $this->createMock(CacheProvider::class);
        $this->cache = new Cache($this->cacheProvider);
    }

    public function testGetValidResponse(): void
    {
        $this->cacheProvider->expects($this->once())->method('save');
        $this->cacheProvider->expects($this->never())->method('fetch');
        $request = new Request('GET', '/');
        $response = new Response();

        /** @var Closure $closure */
        $closure = call_user_func($this->cache, function () use ($response): PromiseInterface {
            return new FulfilledPromise($response);
        });

        $closure($request, [])->wait(true);
    }

    public function testInvalidResponse(): void
    {
        $this->cacheProvider->expects($this->never())->method('save');
        $this->cacheProvider->expects($this->never())->method('fetch');
        $request = new Request('GET', '/');
        $response = new Response(500);

        /** @var Closure $closure */
        $closure = call_user_func($this->cache, function () use ($response): PromiseInterface {
            return new FulfilledPromise($response);
        });

        $closure($request, [])->wait(true);
    }

    public function testValidResponseFromCache(): void
    {
        $this->cacheProvider->method('contains')->willReturn(true);
        $this->cacheProvider->method('fetch')->willReturn(new CachedResponse([], 'body'));
        $this->cacheProvider->expects($this->never())->method('save');
        $this->cacheProvider->expects($this->once())->method('fetch');
        $request = new Request('GET', '/');
        $response = new Response();

        /** @var Closure $closure */
        $closure = call_user_func($this->cache, function () use ($response): PromiseInterface {
            return new FulfilledPromise($response);
        });

        /** @var Response $response */
        $response = $closure($request, [])->wait(true);
        $this->assertSame([], $response->getHeaders());
        $this->assertSame('body', (string) $response->getBody());
    }
}
