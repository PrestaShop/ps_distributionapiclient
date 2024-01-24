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

namespace PrestaShop\Module\DistributionApiClient;

use PrestaShop\CircuitBreaker\Contract\CircuitBreakerInterface;
use PrestaShop\PrestaShop\Adapter\Module\ModuleDataProvider;
use PrestaShop\PrestaShop\Core\Module\SourceHandler\SourceHandlerFactory;
use RuntimeException;

class DistributionApi
{
    public const ALLOWED_FAILURES = 2;
    public const TIMEOUT_IN_SECONDS = 3;
    public const THRESHOLD_SECONDS = 86400; // 24 hours
    public const CACHE_LIFETIME_SECONDS = 86400; // 24 hours
    public const URL_TRACKING_ENV_NAME = 'PS_URL_TRACKING';

    private const API_ENDPOINT = 'https://api.prestashop-project.org';

    /** @var CircuitBreakerInterface */
    private $circruitBreaker;

    /** @var SourceHandlerFactory */
    private $sourceHandlerFactory;

    /** @var ModuleDataProvider */
    private $moduleDataProvider;

    /** @var string */
    private $prestashopVersion;

    /** @var string */
    private $downloadDirectory;

    /** @var ShopDataProvider */
    private $shopDataProvider;

    public function __construct(
        CircuitBreakerInterface $circruitBreaker,
        SourceHandlerFactory $sourceHandlerFactory,
        ModuleDataProvider $moduleDataProvider,
        ShopDataProvider $shopDataProvider,
        string $prestashopVersion,
        string $downloadDirectory
    ) {
        $this->circruitBreaker = $circruitBreaker;
        $this->sourceHandlerFactory = $sourceHandlerFactory;
        $this->moduleDataProvider = $moduleDataProvider;
        $this->prestashopVersion = $prestashopVersion;
        $this->downloadDirectory = rtrim($downloadDirectory, '/');
        $this->shopDataProvider = $shopDataProvider;
    }

    /**
     * @return array<array<string, string>>
     */
    public function getModuleList(): array
    {
        $endpoint = $this->getModulesListUrl();
        $response = $this->getResponse($endpoint);

        $modules = [];

        foreach ($response as $name => $module) {
            $attributes = [
                'name' => $name,
                'version_available' => $module['version'],
                'download_url' => $module['download_url'],
            ];
            if (!$this->isModuleOnDisk($name)) {
                $attributes += [
                    'displayName' => $module['display_name'],
                    'description' => $module['description'],
                    'version' => $module['version'],
                    'author' => $module['author'],
                    'img' => $module['icon'],
                    'tab' => $module['tab'],
                ];
            }
            $modules[] = $attributes;
        }

        return $modules;
    }

    public function downloadModule(string $moduleName): void
    {
        $modules = $this->getModuleList();
        foreach ($modules as $module) {
            if ($module['name'] === $moduleName) {
                $this->doDownload($module);
                break;
            }
        }
    }

    public function isModuleOnDisk(string $moduleName): bool
    {
        return $this->moduleDataProvider->isOnDisk($moduleName);
    }

    /**
     * Extracts the download URL from a module data structure
     *
     * @param array{download_url?: string} $module Module data structure, from API response
     *
     * @return string Download URL
     */
    protected function getModuleDownloadUrl(array $module): string
    {
        if (!isset($module['download_url'])) {
            throw new RuntimeException('Could not determine URL to download the module from');
        }

        return $this->addShopInfoToUrl($module['download_url']);
    }

    /**
     * Returns the URL to the list of modules for this version
     *
     * @return string
     */
    private function getModulesListUrl(): string
    {
        $url = self::API_ENDPOINT . '/modules/' . $this->prestashopVersion;

        return $this->addShopInfoToUrl($url);
    }

    /**
     * Adds shop information to an URL
     *
     * @param string $url API endpoint
     *
     * @return string Modified URL
     */
    private function addShopInfoToUrl(string $url): string
    {
        if (isset($_SERVER[self::URL_TRACKING_ENV_NAME])
            && ((bool) $_SERVER[self::URL_TRACKING_ENV_NAME] === false || $_SERVER[self::URL_TRACKING_ENV_NAME] === 'false')
        ) {
            return $url;
        }

        $shopUrl = urlencode($this->shopDataProvider->getShopUrl());

        $separator = (strpos($url, '?') !== false) ? '&' : '?';

        return sprintf('%s%sshop_domain=%s', $url, $separator, $shopUrl);
    }

    /**
     * @param array<string, string> $module
     *
     * @return void
     */
    private function doDownload(array $module): void
    {
        $downloadUrl = $this->getModuleDownloadUrl($module);

        $moduleZip = file_get_contents($downloadUrl);

        $downloadPath = $this->getModuleDownloadDirectory($module['name']);
        $this->createDownloadDirectoryIfNeeded($downloadPath);

        file_put_contents($this->getModuleDownloadDirectory($module['name']), $moduleZip);

        $handler = $this->sourceHandlerFactory->getHandler($this->getModuleDownloadDirectory($module['name']));
        $handler->handle($this->getModuleDownloadDirectory($module['name']));
    }

    private function getModuleDownloadDirectory(string $moduleName): string
    {
        return $this->downloadDirectory . '/' . $moduleName . '.zip';
    }

    private function createDownloadDirectoryIfNeeded(string $downloadPath): void
    {
        if (!file_exists(dirname($downloadPath))) {
            mkdir(dirname($downloadPath), 0777, true);
        }
    }

    /**
     * @param string $endpoint
     *
     * @return array<array<string, string>>
     */
    private function getResponse(string $endpoint): array
    {
        $response = $this->circruitBreaker->call($endpoint, [], function () {
            throw new \PrestaShopException('Unable to retrieve informations from Distribution API : cannot automatically update native modules for the moment.');
        });

        /** @var array<array<string, string>> $json */
        $json = json_decode($response, true) ?: [];

        return $json;
    }
}
