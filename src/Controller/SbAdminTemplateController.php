<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SbAdminTemplateController extends AbstractController
{
    #[Route('/sb/admin/template', name: 'app_sb_admin_template')]
    public function index(): Response
    {
        return $this->render('sb_admin_template/index.html.twig', [
            'controller_name' => 'SbAdminTemplateController',
        ]);
    }
}
