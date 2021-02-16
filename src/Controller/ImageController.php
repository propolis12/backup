<?php

namespace App\Controller;


use App\Entity\Image;
use App\Form\ImageUpdateType;
use App\Repository\ImageRepository;
use App\Service\UploaderHelper;
use Doctrine\ORM\EntityManagerInterface;
use ImagickException;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
     *
     *@Route("/sphotos/{filename}" , name="send_file" , methods={"GET"} )
     */
    public function sendRequestedPhotoAfterAuthentification(Request $request , string $filename , KernelInterface $kernel) {

        $image = $this->imageRepository->findOneBy(['filename' => $filename]);
        if ($image->getOwner() === $this->security->getUser()) {
            $imagePath = $this->uploaderHelper->getFullPath($image->getFilename());
            $response = new BinaryFileResponse($imagePath);
            return $response;
        }
        //dd($response);

        return  new JsonResponse("Not authorized to view this photo", 401);
    //kernel->getProjectDir().UploaderHelper::IMAGE_DIRECTORY.'/'.$image->getFilename()
    }

//'/home/jakub/Documents/securitySkuska/var/uploadedPhotos/satellite-image-of-globe-602321652ec51.jpg'


    /**
     * @Route("/photo/{filename}", name="send_thumbnail", methods={"GET"} )
     *
     * @param string $filename
     * @throws ImagickException
     */

    public function thumbnailImage(Request $request, string $filename) {

        $publicName = $this->uploaderHelper->getFullPath($filename);
        $imagick = new \Imagick($publicName);
        $imagick->setbackgroundcolor('rgb(64, 64, 64)');
        $imagick->thumbnailImage(300, 300, true, true);
        header("Content-Type: image/jpg");
        /*$response = new BinaryFileResponse($imagick->getFilename());
        return $response;*/
        echo $imagick->getImageBlob();

    }

    /**
     * @Route("/pphoto/{originalName}" , name="latest_image", methods={"GET"})
     * @param string $originalName
     */
    public function latestPhotoThumbnailImage(string $originalName) {
        $images = $this->imageRepository->findBy(['originalName' => $originalName]);

        /*if ($image->getOwner() === $this->security->getUser()) {
            $imagePath = $this->uploaderHelper->getFullPath($image->getFilename());
            $response = new BinaryFileResponse($imagePath);
            return $response;
        }*/

    }

    /**
     * @Route("/owned/images", name="get_owned_images", methods={"GET"})
     */
    public function ownedImages(Request $request) {
        $ownedImages = $this->imageRepository->getOwnedImagesFilenames(null, null);
        //$ownedImages = $this->getUser()->getImages();
        //print_r($ownedImages);
       // dd($ownedImages);
        return new JsonResponse($ownedImages, 200);

    }

    /**
     *@Route("/upload/dropzone", name="dropzone_upload")
     */
    public function handleDropzone(Request $request , UploaderHelper $uploaderHelper , Security $security , EntityManagerInterface $entityManager) {
        /** @var UploadedFile $uploadedFile */
        $uploadedFile = $request->files->get('dropzone');

        /** @var Image $image */
        $image = new Image();
        $newFilename = $uploaderHelper->uploadFile($uploadedFile);
        $image->setPublic(false);
        $image->setFilename($newFilename);
        $image->setOwner($security->getUser());
        $image->setUploadedAt(new \DateTimeImmutable("now"));
        $image->setOriginalName($uploadedFile->getFilename());
        $entityManager->persist($image);
        $entityManager->flush();
        return new JsonResponse("OK", 200);

        /* $uploadedFile = $request->files->get('dropzone');
         dump($uploadedFile);
           ;*/

    }

    /**
     * @Route("/latest/uploaded/photo" , name="latest_photo" , methods={"GET"})
     * @throws ImagickException
     */
    public function getLatestPhotoUploadedName() {
        $number = $this->imageRepository->getLastOwnedId();
        //dd($number[0][1]);
        /** @var Image $image */
        $image = $this->imageRepository->findOneBy(['id' => $number[0][1]]);
        //dd($image);
        //$publicName = $this->uploaderHelper->getFullPath($image->getFilename());
        /*$imagick = new \Imagick($publicName);
        $imagick->setbackgroundcolor('rgb(64, 64, 64)');
        $imagick->thumbnailImage(300, 300, true, true);
        header("Content-Type: image/jpg");
        /*$response = new BinaryFileResponse($imagick->getFilename());
        return $response;*/
        //echo $imagick->getImageBlob();

        //print_r($photo);
        return new JsonResponse($image->getFilename(), 200);


    }



}
