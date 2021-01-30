<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainPageController extends AbstractController
{
    /**
     * @Route("/", name="main_page")
     */
    public function index(Request $request): Response
    {
        $message= 'sddfsf';
        if ($message = $request->query->get('message')) {}

        return $this->render('main_page/MainPage.html.twig', [
            'message' => $message,
        ]);
    }
}
