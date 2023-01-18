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
    /** @var string Name of the submit button */
    public const SUBMIT_NAME = 'update-configuration';

    /** @var string Config key for the setting */
    public const SEND_SHOP_URL = 'DISTRIBUTIONAPI_SEND_SHOP_URL';

    public function __construct()
    {
        $this->name = 'ps_distributionapiclient';
        $this->displayName = $this->trans('Distribution API Client', [], 'Modules.Distributionapiclient.Admin');
        $this->description = $this->trans('Download and upgrade PrestaShop\'s native modules.', [], 'Modules.Distributionapiclient.Admin');
        $this->author = 'PrestaShop';
        $this->version = '1.1.0';
        $this->ps_versions_compliancy = ['min' => '8.0.0', 'max' => _PS_VERSION_];
        $this->tab = 'market_place';
        $this->bootstrap = true;
        parent::__construct();
    }

    public function install(): bool
    {
        return parent::install()
            && Configuration::updateValue(static::SEND_SHOP_URL, 1)
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

    private function getDistributionApi(): DistributionApi
    {
        /** @var DistributionApi $distributionApi */
        $distributionApi = $this->get('distributionapiclient.distribution_api');

        return $distributionApi;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        $html = $this->renderForm();

        if (Tools::isSubmit(static::SUBMIT_NAME)) {
            Configuration::updateValue(
                static::SEND_SHOP_URL,
                Tools::getValue(static::SEND_SHOP_URL)
            );

            /** @var Link $link */
            $link = $this->context->link;
            Tools::redirectAdmin($link->getAdminLink('AdminModules') . '&configure=' . $this->name . '&conf=6');
        }

        return $html;
    }

    /**
     * @return string
     */
    protected function renderForm()
    {
        $fieldsValue = [
            static::SEND_SHOP_URL => Tools::getValue(
                static::SEND_SHOP_URL,
                Configuration::get(static::SEND_SHOP_URL)
            ),
        ];
        $form = [
            'form' => [
                'legend' => [
                    'title' => $this->trans('Parameters', [], 'Modules.Distributionapiclient.Admin'),
                    'icon' => 'icon-envelope',
                ],
                'input' => [
                    [
                        'type' => 'switch',
                        'label' => $this->trans(
                            'Share this store\'s public URL with PrestaShop',
                            [],
                            'Modules.Distributionapiclient.Admin'
                        ),
                        'desc' => $this->trans(
                            "If this option is enabled, the URL to your store's front office will be sent to PrestaShop alongside distribution API requests. Sharing this information with us helps better understand how PrestaShop software is used in the ecosystem.",
                            [],
                            'Modules.Distributionapiclient.Admin'
                        ),
                        'name' => self::SEND_SHOP_URL,
                        'is_bool' => true,
                        'required' => true,
                        'values' => [
                            [
                                'id' => self::SEND_SHOP_URL . '_on',
                                'value' => 1,
                                'label' => $this->trans('Enabled', [], 'Admin.Global'),
                            ],
                            [
                                'id' => self::SEND_SHOP_URL . '_off',
                                'value' => 0,
                                'label' => $this->trans('Disabled', [], 'Admin.Global'),
                            ],
                        ],
                    ],
                ],
                'submit' => [
                    'name' => self::SUBMIT_NAME,
                    'title' => $this->trans('Save', [], 'Admin.Actions'),
                ],
            ],
        ];
        $helper = new HelperForm();
        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->submit_action = self::SUBMIT_NAME;
        /** @var Link $link */
        $link = $this->context->link;
        $helper->currentIndex = $link->getAdminLink('AdminModules') . '&configure=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        /** @var AdminController $adminController */
        $adminController = $this->context->controller;
        /** @var Language $language */
        $language = $this->context->language;
        $helper->tpl_vars = [
            'fields_value' => $fieldsValue,
            'languages' => $adminController->getLanguages(),
            'id_language' => $language->id,
        ];

        return $helper->generateForm([$form]);
    }
}
