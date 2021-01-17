<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\FormRegisterUserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/register", name="app_registration")
     */
    public function register(Request $request, UserPasswordEncoderInterface $userPasswordEncoder )
    {

        $form = $this->createForm(FormRegisterUserType::class);
        $form->handleRequest($request);
        $error = '';
        if($form->isSubmitted() && $form->isValid() && $request->isMethod('POST')) {

            /** @var  User $user */
            $user = $form->getData();
            $user->setPassword($userPasswordEncoder->encodePassword($user , $form['Password']->getData()));
            $user->setRegisteredAt(new \DateTime("now"));
            $user->setRoles(['ROLE_USER']);

            $em = $this->getDoctrine()->getManager();

            try {
                $em->persist($user);
                $em->flush();
            } catch (\Exception $e) {
                $error = "zadane uzivatelske meno sa pouziva";

            }


        }

        return $this->render('security/register.html.twig', [
            'registrationForm' => $form->createView(),
                'error' => $error
            ]

        );
    }
}
