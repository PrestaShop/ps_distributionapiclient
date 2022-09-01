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

use PrestaShop\Module\DistributionApiClient\DistributionApi;

if (!defined('_PS_VERSION_')) {
    exit;
}

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

class Ps_Distributionapiclient extends Module
{
    public function __construct()
    {
        $this->name = 'ps_distributionapiclient';
        $this->displayName = $this->trans('Distribution API Client', [], 'Modules.Distributionapiclient.Admin');
        $this->description = $this->trans('Download and upgrade PrestaShop\'s native modules.', [], 'Modules.Distributionapiclient.Admin');
        $this->author = 'PrestaShop';
        $this->version = '1.0.2';
        $this->ps_versions_compliancy = ['min' => '8.0.0', 'max' => _PS_VERSION_];
        $this->tab = 'market_place';
        parent::__construct();
    }

    public function install(): bool
    {
        return parent::install()
            && $this->registerHook('actionListModules')
            && $this->registerHook('actionBeforeInstallModule')
            && $this->registerHook('actionBeforeUpgradeModule')
        ;
    }

    /**
     * @return array<array<string, string>>
     */
    public function hookActionListModules(): array
    {
        return $this->getDistributionApi()->getModuleList();
    }

    /**
     * @param string[] $params
     *
     * @return void
     */
    public function hookActionBeforeInstallModule(array $params): void
    {
        if (!isset($params['moduleName']) || $this->getDistributionApi()->isModuleOnDisk($params['moduleName'])) {
            return;
        }

        $this->getDistributionApi()->downloadModule($params['moduleName']);
    }

    /**
     * @param string[] $params
     *
     * @return void
     */
    public function hookActionBeforeUpgradeModule(array $params): void
    {
        if (!isset($params['moduleName'])) {
            return;
        }

        $this->getDistributionApi()->downloadModule($params['moduleName']);
    }

    private function getDistributionApi(): DistributionApi
    {
        /** @var DistributionApi $distributionApi */
        $distributionApi = $this->get('distributionapiclient.distribution_api');

        return $distributionApi;
    }
}
