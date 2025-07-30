<?php

namespace PrestaShop\Module\DistributionApiClient\Controller\Admin;

use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TopContributorsController extends FrameworkBundleAdminController
{
    /**
     * @Route("/ps_distributionapiclient/top-contributors", name="ps_distributionapiclient_top_contributors")
     */
    public function index(): Response
    {
        return $this->render('@Modules/ps_distributionapiclient/views/templates/admin/top_contributors.html.twig', [
            'enableSidebar' => false,
            'showContentHeader' => true,
        ]);
    }
}
