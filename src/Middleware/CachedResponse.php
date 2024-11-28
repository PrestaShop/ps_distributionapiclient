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

use Symfony\Component\HttpClient\Exception\JsonException;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * Simple DTO containing the response data to allow serializing it into cache.
 */
class CachedResponse implements ResponseInterface
{
    private int $statusCode;

    /**
     * @var string[][]
     */
    private array $headers;
    private string $content;

    /**
     * @var mixed[]|array|null
     */
    private ?array $jsonData = null;

    /**
     * @var array<string, mixed>
     */
    private array $info;

    public function __construct(ResponseInterface $response)
    {
        $info = $response->getInfo();
        if (is_array($info)) {
            $this->info = [
                'canceled' => $info['canceled'] ?? false,
                'error' => $info['error'] ?? null,
                'http_code' => $info['http_code'] ?? 0,
                'http_method' => $info['http_method'] ?? 'GET',
                'redirect_count' => $info['redirect_count'] ?? 0,
                'redirect_url' => $info['redirect_url'] ?? null,
                'start_time' => $info['start_time'] ?? 0.0,
                'url' => $info['url'] ?? '',
                'user_data' => $info['user_data'] ?? null,
            ];
        } elseif (is_object($info)) {
            $this->info = [
                'canceled' => property_exists($info, 'canceled') ? $info->canceled : false,
                'error' => property_exists($info, 'error') ? $info->error : null,
                'http_code' => property_exists($info, 'http_code') ? $info->http_code : 0,
                'http_method' => property_exists($info, 'http_method') ? $info->http_method : 'GET',
                'redirect_count' => property_exists($info, 'redirect_count') ? $info->redirect_count : 0,
                'redirect_url' => property_exists($info, 'redirect_url') ? $info->redirect_url : null,
                'start_time' => property_exists($info, 'start_time') ? $info->start_time : 0.0,
                'url' => property_exists($info, 'url') ? $info->url : '',
                'user_data' => property_exists($info, 'user_data') ? $info->user_data : null,
            ];
        } else {
            $this->info = [
                'canceled' => false,
                'error' => null,
                'http_code' => 0,
                'http_method' => 'GET',
                'redirect_count' => 0,
                'redirect_url' => null,
                'start_time' => 0.0,
                'url' => '',
                'user_data' => null,
            ];
        }

        $this->statusCode = $response->getStatusCode();
        $this->headers = $response->getHeaders(false);
        $this->content = $response->getContent(false);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getHeaders(bool $throw = true): array
    {
        return $this->headers;
    }

    public function getContent(bool $throw = true): string
    {
        return $this->content;
    }

    /**
     * @param bool $throw
     *
     * @return array|mixed[]
     */
    public function toArray(bool $throw = true): array
    {
        // Code copied from CommonResponseTrait
        if ('' === $content = $this->getContent($throw)) {
            throw new JsonException('Response body is empty.');
        }

        if (null !== $this->jsonData) {
            return $this->jsonData;
        }

        try {
            $content = json_decode($content, true, 512, \JSON_BIGINT_AS_STRING | \JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            /** @var string $url */
            $url = $this->getInfo('url');
            throw new JsonException($e->getMessage() . sprintf(' for "%s".', $url), $e->getCode());
        }

        if (!\is_array($content)) {
            /** @var string $url */
            $url = $this->getInfo('url');
            throw new JsonException(sprintf('JSON content was expected to decode to an array, "%s" returned for "%s".', get_debug_type($content), $url));
        }

        return $this->jsonData = $content;
    }

    public function cancel(): void
    {
    }

    public function getInfo(?string $type = null): mixed
    {
        if (null !== $type) {
            return $this->info[$type] ?? null;
        }

        return $this->info;
    }
}
