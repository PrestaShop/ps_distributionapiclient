<?php

namespace PrestaShop\Module\DistributionApiClient\Controller\Admin;

use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Twig\Layout\MenuLink;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TopContributorsController extends PrestaShopAdminController
{
    private const TRANSLATION_DOMAIN = 'Modules.Distributionapiclient.Admin';

    /**
     * @Route("/ps_distributionapiclient/top-contributors", name="ps_distributionapiclient_top_contributors")
     */
    public function index(): Response
    {
        return $this->render('@Modules/ps_distributionapiclient/views/templates/admin/top_contributors.html.twig', [
            'enableSidebar' => false,
            'showContentHeader' => true,
            'layoutTitle' => $this->trans('Wall of Fame', [], self::TRANSLATION_DOMAIN),
            // The toolbar draws no breadcrumb unless it is given one, so the page arrived without
            // the trail its menu entry has. The wording and the icon are the ones registerTab()
            // uses, so the trail matches the menu rather than restating it differently.
            'breadcrumbLinks' => [
                'container' => new MenuLink($this->trans('Community', [], self::TRANSLATION_DOMAIN)),
                'tab' => new MenuLink(
                    $this->trans('Wall of Fame', [], self::TRANSLATION_DOMAIN),
                    $this->generateUrl('ps_distributionapiclient_top_contributors'),
                    'groups'
                ),
            ],
        ]);
    }
}
