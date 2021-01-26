<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/index", name="main")
     */
    public function index(): Response
    {
        return $this->render('main_page/MainPage.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }
}
