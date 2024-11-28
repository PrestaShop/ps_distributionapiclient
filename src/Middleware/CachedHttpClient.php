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

namespace PrestaShop\Module\DistributionApiClient\Middleware;

use Doctrine\Common\Cache\CacheProvider;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\HttpClient\ResponseStreamInterface;

class CachedHttpClient implements HttpClientInterface
{
    private CacheProvider $cache;

    private HttpClientInterface $client;

    /**
     * @param CacheProvider $cache
     * @param array<string, mixed> $defaultOptions
     * @param HttpClientInterface|null $client
     */
    public function __construct(CacheProvider $cache, array $defaultOptions = [], ?HttpClientInterface $client = null)
    {
        $this->cache = $cache;
        $this->client = $client ?? HttpClient::create($defaultOptions);
    }

    /**
     * @param string $method
     * @param string $url
     * @param array<string, mixed> $options
     *
     * @return ResponseInterface
     */
    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        $cacheKey = $this->getCacheKey($method, $url);
        if ($this->cache->contains($cacheKey)) {
            /** @var CachedResponse $cachedResponse */
            $cachedResponse = $this->cache->fetch($cacheKey);

            return $cachedResponse;
        }

        $response = $this->client->request($method, $url, $options);
        if ($response->getStatusCode() !== 200) {
            return $response;
        }

        $cachedResponse = new CachedResponse($response);
        $this->cache->save($cacheKey, $cachedResponse);

        return $cachedResponse;
    }

    public function stream($responses, ?float $timeout = null): ResponseStreamInterface
    {
        return $this->client->stream($responses, $timeout);
    }

    /**
     * @param array<string, mixed> $options
     *
     * @return static
     */
    public function withOptions(array $options): static
    {
        // @phpstan-ignore-next-line
        return new static($this->cache, $options);
    }

    private function getCacheKey(string $method, string $url): string
    {
        return md5($method . $url);
    }
}
