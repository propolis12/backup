<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class VueMainPageController extends AbstractController
{

    private $security;
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @Route("/sfgdf", name="vue_main_page")
     *
     */
    public function index(): Response
    {

        $isUserAdmin = $this->security->isGranted("ROLE_ADMIN");
        return $this->render('main_page/vueMainPage.html.twig', [
            'isAdmin' => $isUserAdmin
        ]);
    }
}
