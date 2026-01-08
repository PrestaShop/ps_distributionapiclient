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

namespace PrestaShop\Module\DistributionApiClient\Controller\Admin;

use PrestaShop\Module\DistributionApiClient\Form\Type\ConfigurationType;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Entity\Repository\TabRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ConfigurationController extends PrestaShopAdminController
{
    private const WALL_OF_FAME_TAB_CLASS_NAME = 'AdminPsdistributionapiclient';

    private TabRepository $tabRepository;

    public function __construct(TabRepository $tabRepository)
    {
        $this->tabRepository = $tabRepository;
    }

    public function index(Request $request): Response
    {
        $form = $this->createForm(ConfigurationType::class, [
            'wall_of_fame_enabled' => $this->isWallOfFameTabEnabled(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->tabRepository->changeStatusByClassName(self::WALL_OF_FAME_TAB_CLASS_NAME, (bool) $form->getData()['wall_of_fame_enabled']);

            $this->addFlash('success', $this->trans('Settings updated.', [], 'Admin.Notifications.Success'));

            return $this->redirectToRoute('ps_distributionapiclient_configuration');
        }

        return $this->render('@Modules/ps_distributionapiclient/views/templates/admin/configure.html.twig', [
            'configurationForm' => $form->createView(),
            'enableSidebar' => true,
            'layoutTitle' => $this->trans('Wall of Fame configuration', [], 'Modules.Distributionapiclient.Admin'),
            'form_theme' => '@PrestaShop/Admin/TwigTemplateForm/prestashop_ui_kit.html.twig',
        ]);
    }

    private function isWallOfFameTabEnabled(): bool
    {
        return $this->tabRepository->findOneByClassName(self::WALL_OF_FAME_TAB_CLASS_NAME)->getActive();
    }
}
