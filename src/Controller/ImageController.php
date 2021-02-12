<?php

namespace App\Controller;


use App\Form\ImageUpdateType;
use App\Repository\ImageRepository;
use App\Service\UploaderHelper;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class ImageController extends AbstractController
{


    /**
     * @var Security
     */
    private Security $security;
    /**
     * @var ImageRepository
     */
    private ImageRepository $imageRepository;
    /**
     * @var UploaderHelper
     */
    private UploaderHelper $uploaderHelper;

    public function __construct(Security $security , ImageRepository $imageRepository ,  UploaderHelper $uploaderHelper) {
        $this->security = $security;
        $this->imageRepository = $imageRepository;
        $this->uploaderHelper = $uploaderHelper;
    }


    /**
     * @Route("/image", name="image")
     */
    public function index(): Response
    {
        return $this->render('image/index.html.twig', [
            'controller_name' => 'ImageController',
        ]);
    }



    /**
     * @Route("/upload/photo/{id}" , name="upload_photo")
     */
    public function uploadPhoto(Request $request) {


        return $this->redirectToRoute('upload_photo', [ 'id' => $this->security->getUser()->getId() , 'message' => " Photo uploaded!"]);

    }

    /**
     * @Route("/upload/{id}", name="upload_test")
     */
    public function temporaryUploadAction(Request $request)
    {
        $form = $this->createForm(ImageUpdateType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            dd($form['imageFilename']->getData());
        }
        return $this->render('main_page/MainPage.html.twig', [
            'uploadForm' => $form->createView(),
        ]);
    }


    /**
     *
     *@Route("/photos/{filename}" , name="send_file" , methods={"GET"} )
     */
    public function sendRequestedPhotoAfterAuthentification(Request $request , string $filename , KernelInterface $kernel) {

        $image = $this->imageRepository->findOneBy(['filename' => $filename]);
        if ($image->getOwner() === $this->security->getUser()) {
            $imagePath = $this->uploaderHelper->getPublicPath($image->getFilename());
            $response = new BinaryFileResponse($kernel->getProjectDir().'/photos/'.$image->getFilename());
            return $response;
        }
        //dd($response);

        return  new JsonResponse("Not authorized to view this photo", 401);
    //kernel->getProjectDir().UploaderHelper::IMAGE_DIRECTORY.'/'.$image->getFilename()
    }

//'/home/jakub/Documents/securitySkuska/var/uploadedPhotos/satellite-image-of-globe-602321652ec51.jpg'

}
