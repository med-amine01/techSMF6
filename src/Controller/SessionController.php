<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SessionController extends AbstractController
{
    #[Route('/session', name: 'session')]
    public function index(Request $request): Response
    {
        $session = $request->getSession();
        if($session->has('nbvisite'))
        {
            $nb = $session->get('nbvisite') + 1;
        }
        else
        {
            $nb = 1;
        }
        $session->set('nbvisite',$nb);

        return $this->render('session/index.html.twig');
    }
}
