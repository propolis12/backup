<?php

namespace App\Controller;


use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\Image;
use App\Form\ImageUpdateType;

use App\Repository\ImageRepository;
use App\Service\UploaderHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
//use Symfony\Component\HttpFoundation\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class MainPageController extends AbstractController
{



    public function __construct(KernelInterface $kernel)
  {
      $this->kernel = $kernel;
  }

    /**
     * @Route("/", name="main_page")
     */
    public function uploadFileAction(Request $request , EntityManagerInterface $entityManager, Security $security , UploaderHelper $uploaderHelper, ImageRepository $imageRepository , ValidatorInterface $validator): Response
    {
        if ($message = $request->query->get('message')) {}
        if(!$this->getUser()) {

            return $this->redirectToRoute('app_login', ['message' => "you must log in"] );
        }

        $ownedFiles = $imageRepository->findBy(['owner' => $security->getUser()->getId()]);

        //dd($ownedFiles);
        $form = $this->createForm(ImageUpdateType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $form['image']->getData();

            /** @var Image $image */
            $image = new Image();
            $newFilename = $uploaderHelper->uploadFile($uploadedFile);
            $image->setPublic(false);
            $image->setFilename($newFilename);
            $image->setOwner($security->getUser());
            $image->setUploadedAt(new \DateTimeImmutable("now"));
            $entityManager->persist($image);
            $entityManager->flush();
        }
        return $this->render('main_page/MainPage.html.twig', [
            'uploadForm' => $form->createView(),
            'message' => $message,
            'ownedImages' => $ownedFiles,
        ]);
    }






}
