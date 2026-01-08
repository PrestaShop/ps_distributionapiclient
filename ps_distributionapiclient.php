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
    const WALL_OF_FAME_TAB_CLASS_NAME = 'AdminPsdistributionapiclient';

    public function __construct()
    {
        $this->name = 'ps_distributionapiclient';
        $this->displayName = $this->trans('Distribution API Client', [], 'Modules.Distributionapiclient.Admin');
        $this->description = $this->trans('Download and upgrade PrestaShop\'s native modules.', [], 'Modules.Distributionapiclient.Admin');
        $this->author = 'PrestaShop';
        $this->version = '2.1.1';
        $this->ps_versions_compliancy = ['min' => '9.0.0', 'max' => _PS_VERSION_];
        $this->tab = 'market_place';
        parent::__construct();
    }

    public function install(): bool
    {
        return parent::install()
            && $this->registerHook('actionListModules')
            && $this->registerHook('actionBeforeInstallModule')
            && $this->registerHook('actionBeforeUpgradeModule')
            && $this->registerTab()
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
        $distributionApi = $this->getDistributionApi();
        if (!isset($params['moduleName']) || $distributionApi->isModuleOnDisk($params['moduleName'])) {
            return;
        }

        $distributionApi->downloadModule($params['moduleName']);
    }

    /**
     * @param string[] $params
     *
     * @return void
     */
    public function hookActionBeforeUpgradeModule(array $params): void
    {
        if (!isset($params['moduleName']) || !empty($params['source'])) {
            return;
        }

        $this->getDistributionApi()->downloadModule($params['moduleName']);
    }

    public function getContent(): string
    {
        if (Tools::isSubmit('submitWallOfFameConfig')) {
            $this->setWallOfFameTabStatus((bool) Tools::getValue('WALL_OF_FAME_ENABLED'));

            Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules') . '&configure=' . $this->name . '&conf=6');
        }

        return $this->renderForm();
    }

    private function renderForm(): string
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitWallOfFameConfig';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = [
            'fields_value' => [
                'WALL_OF_FAME_ENABLED' => $this->isWallOfFameTabEnabled(),
            ],
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        return $helper->generateForm([$this->getConfigForm()]);
    }

    private function getConfigForm(): array
    {
        return [
            'form' => [
                'legend' => [
                    'title' => $this->trans('Wall of Fame configuration', [], 'Modules.Distributionapiclient.Admin'),
                    'icon' => 'icon-cogs',
                ],
                'description' => $this->trans('The Wall of Fame tab displays the top contributors to the PrestaShop project. You can enable or disable this tab in the back office menu.', [], 'Modules.Distributionapiclient.Admin'),
                'input' => [
                    [
                        'type' => 'switch',
                        'label' => $this->trans('Enable Wall of Fame Tab', [], 'Modules.Distributionapiclient.Admin'),
                        'name' => 'WALL_OF_FAME_ENABLED',
                        'desc' => $this->isWallOfFameTabEnabled()
                            ? $this->trans('The Wall of Fame tab is currently visible in the back office menu under Community.', [], 'Modules.Distributionapiclient.Admin')
                            : $this->trans('The Wall of Fame tab is currently hidden from the back office menu.', [], 'Modules.Distributionapiclient.Admin'),
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->trans('Enabled', [], 'Admin.Global'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->trans('Disabled', [], 'Admin.Global'),
                            ],
                        ],
                    ],
                ],
                'submit' => [
                    'title' => $this->trans('Save', [], 'Admin.Actions'),
                ],
            ],
        ];
    }

    private function isWallOfFameTabEnabled(): bool
    {
        $tabId = Tab::getIdFromClassName(self::WALL_OF_FAME_TAB_CLASS_NAME);
        if (!$tabId) {
            return false;
        }

        $tab = new Tab($tabId);

        return (bool) $tab->active;
    }

    private function setWallOfFameTabStatus(bool $enabled): void
    {
        $tabId = Tab::getIdFromClassName(self::WALL_OF_FAME_TAB_CLASS_NAME);
        if ($tabId) {
            $tab = new Tab($tabId);
            $tab->active = $enabled;
            $tab->save();
        }
    }

    private function getDistributionApi(): DistributionApi
    {
        /** @var DistributionApi $distributionApi */
        $distributionApi = $this->get('distributionapiclient.distribution_api');

        return $distributionApi;
    }

    public function registerTab(): bool
    {
        $parentClass = 'AdminPsdistributionapiclientCommunity';
        $parentTabId = Tab::getIdFromClassName($parentClass);
        $parentTab = new Tab($parentTabId ?: null);
        $parentTab->active = true;
        $parentTab->class_name = $parentClass;
        $parentTab->id_parent = 0;
        $parentTab->module = $this->name;
        $parentTab->wording = 'Community';
        $parentTab->wording_domain = 'Modules.Distributionapiclient.Admin';
        /** @var array{'id_lang': int, "locale": string} $lang */
        foreach (Language::getLanguages() as $lang) {
            $parentTab->name[$lang['id_lang']] = $this->trans('Community', [], 'Modules.Distributionapiclient.Admin', $lang['locale']);
        }
        $parentTab->save();

        // Creation of the sub tab "Wall of Fame"
        $childTabId = Tab::getIdFromClassName(self::WALL_OF_FAME_TAB_CLASS_NAME);
        $childTab = new Tab($childTabId ?: null);
        $childTab->active = true;
        $childTab->class_name = self::WALL_OF_FAME_TAB_CLASS_NAME;
        $childTab->id_parent = (int) Tab::getIdFromClassName($parentClass);
        $childTab->route_name = 'ps_distributionapiclient_top_contributors';
        $childTab->module = $this->name;
        $childTab->wording = 'Wall of Fame';
        $childTab->wording_domain = 'Modules.Distributionapiclient.Admin';
        $childTab->icon = 'groups';
        /** @var array{'id_lang': int, "locale": string} $lang */
        foreach (Language::getLanguages() as $lang) {
            $childTab->name[$lang['id_lang']] = $this->trans('Wall of Fame', [], 'Modules.Distributionapiclient.Admin', $lang['locale']);
        }
        $childTab->save();

        return true;
    }
}
