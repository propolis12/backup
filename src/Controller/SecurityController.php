<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\FormRegisterUserType;
use App\Form\FormUpdateUserInfoType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;



class SecurityController extends AbstractController
{

    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {

    $this->manager = $manager;}

    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils , Request $request): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }
        //$message = "";

        if ($message = $request->query->get('message')) {}
            //dd($message);
       /* if (isset($_GET["message"]) && $_GET('message') != "") {
            $message = $_GET["message"];
        }*/

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error, 'message' => $message]);
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
     *
     * @param User $user
     * @param Request $request
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function register( Request $request, UserPasswordEncoderInterface $userPasswordEncoder )
    {

        $form = $this->createForm(FormRegisterUserType::class);
        $form->handleRequest($request);
        $error = '';
        if($form->isSubmitted() && $form->isValid() && $request->isMethod('POST')) {

            /** @var  User $user */
            $user = $form->getData();
            if($form['Username']->getData()) {
                $user->setUsername($form['Username']->getData());
            }
            if($form['Password']->getData()) {
                $user->setPassword($userPasswordEncoder->encodePassword($user, $form['Password']->getData()));
            }
            $user->setRegisteredAt(new \DateTime("now"));
            $user->setRoles(['ROLE_USER']);

            if ($form['agreeTerms']->getData() === true) {
                $user->agreedTermsAt();
            }

            $em = $this->getDoctrine()->getManager();

            //try {
                $em->persist($user);
                $em->flush();

                    return $this->redirectToRoute('app_login', ['message' => "registration successfull, please sign in"] );


           /* } catch (\Exception $e) {
                $error = "zadane uzivatelske meno sa pouziva";

            }*/


        }

        return $this->render('security/register.html.twig', [
            'registrationForm' => $form->createView(),
                'error' => $error
            ]

        );
    }




    /**
     * @param User $user
     * @param Request $request
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     * @param EntityManagerInterface $manager
     * @Route("/user/{id}/update" , name="app_update")
     *
     */
    public function update(User $user,Request $request , UserPasswordEncoderInterface $userPasswordEncoder, EntityManagerInterface $manager) {

        $form = $this->createForm(FormUpdateUserInfoType::class, $user );


        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid() && $request->isMethod('POST')) {
            /** @var  User $user */
            $user = $form->getData();
            if($form['Username']->getData()) {
                $user->setUsername($form['Username']->getData());
            }
            if($form['Password']->getData()) {
                $user->setPassword($userPasswordEncoder->encodePassword($user, $form['Password']->getData()));
            }
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($user);
            $manager->flush();
            return $this->redirectToRoute('main_page', ['message' => " Your personal data successfully updated!"] );
        }



        return $this->render('security/update.html.twig', [
            'updateForm' => $form->createView()
        ]);
    }
}
