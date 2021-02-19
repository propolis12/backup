<?php

namespace App\Controller;

use App\Entity\Image;
use App\Form\ImageUpdateType;
use App\Repository\ImageRepository;
use App\Service\UploaderHelper;
use Doctrine\ORM\EntityManagerInterface;
use ImagickException;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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

    /**
     * @var string[]
     */
    private array $filenamesToRender;
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;


    public function __construct(Security $security , ImageRepository $imageRepository ,  UploaderHelper $uploaderHelper, LoggerInterface $logger ) {
        $this->security = $security;
        $this->imageRepository = $imageRepository;
        $this->uploaderHelper = $uploaderHelper;
        $this->filenamesToRender = array();
        $this->logger = $logger;
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
     *
     * @Route("/latest/photos/{originalFilename}" , name="latest_photos" , methods={"GET"} )
     * @throws ImagickException
     */
    public function latestPhotosAjax(Request $request , string $originalFilename , KernelInterface $kernel) {

        $image = $this->imageRepository->findOneBy(['originalName' => $originalFilename]);
        if ($image->getOwner() === $this->security->getUser()) {
            $imagePath = $this->uploaderHelper->getFullPath($image->getOriginalName());
            //dump($imagePath);
           // print_r($imagePath);
            $imagick = new \Imagick($imagePath);
            $imagick->setbackgroundcolor('rgb(64, 64, 64)');
            $imagick->thumbnailImage(300, 300, true, true);
            header("Content-Type: image/jpg");
            /*$response = new BinaryFileResponse($imagick->getFilename());
            return $response;*/
            echo $imagick->getImageBlob();
           // $response = new BinaryFileResponse($imagePath);
           // return $response;
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
        $image = $this->imageRepository->findOneBy(['originalName' => $filename]);
        if ($image->getOwner() === $this->security->getUser()) {
            $publicName = $this->uploaderHelper->getFullPath($filename);
            $imagick = new \Imagick($publicName);
            $imagick->setbackgroundcolor('rgb(64, 64, 64)');
            $imagick->thumbnailImage(300, 300, true, true);
            header("Content-Type: image/jpg");
            /*$response = new BinaryFileResponse($imagick->getFilename());
            return $response;*/
            echo $imagick->getImageBlob();
        }
    }

    /**
     * @Route("send/photo/{originalName}" , name="latest_image", methods={"GET"})
     * @param string $originalName
     * @return BinaryFileResponse|JsonResponse
     */
    public function latestPhotosByOriginalName(string $originalName) {
        /** @var Image $image */
        $image = $this->imageRepository->findOneBy(['originalName' => $originalName]);

        if ($image->getOwner() === $this->security->getUser()) {
            $imagePath = $this->uploaderHelper->getFullPath($image->getFilename());
            $response = new BinaryFileResponse($imagePath);
            return $response;
        }
        return $this->json("Not authorized to view this photo", 401);

    }

    /**
     * @Route("/owned/images", name="get_owned_images", methods={"GET"})
     */
    public function ownedImages(Request $request) {
        $ownedImages = $this->imageRepository->getOwnedImagesFilenames(null, null);
        //dump($ownedImages);
        //print_r($ownedImages);
        //$this->logger->log($ownedImages,"logging data");
        return new JsonResponse($ownedImages, 200);
    }

    /**
     *@Route("/upload/dropzone", name="dropzone_upload")
     */
    public function handleDropzone(Request $request , UploaderHelper $uploaderHelper , Security $security , EntityManagerInterface $entityManager, ValidatorInterface $validator ) {
        /** @var UploadedFile $uploadedFile */
        $uploadedFile = $request->files->get('dropzone');
        $violations = $validator->validate(
            $uploadedFile,
            new \Symfony\Component\Validator\Constraints\Image(),
        );

        if ($violations->count() > 0) {
            return new JsonResponse("bad file type", 400);
        }
        /** @var Image $image */
        $image = new Image();
        $newFilename = $uploaderHelper->uploadFile($uploadedFile);
         array_push($this->filenamesToRender, $newFilename);
        $image->setPublic(false);
        $image->setFilename($newFilename);
        $image->setOwner($security->getUser());
        $image->setUploadedAt(new \DateTimeImmutable("now"));
        $originalFilename= $uploadedFile->getClientOriginalName();
        $clientNameExtension = pathinfo($originalFilename, PATHINFO_EXTENSION);
        if ($uploadedFile->guessExtension() === 'jpg' || $uploadedFile->guessExtension() === 'png' || $uploadedFile->guessExtension() === 'gif') {
            $newFilename = $originalFilename;
        } else {
            $newFilename = pathinfo($originalFilename, PATHINFO_FILENAME).".".$uploadedFile->guessExtension();
        }
        $image->setOriginalName($newFilename);
        $entityManager->persist($image);
        $entityManager->flush();
        return new JsonResponse("OK", 201);

        /* $uploadedFile = $request->files->get('dropzone');
         dump($uploadedFile);
           ;*/

    }

    /**
     * @Route("/latest/uploaded/photo" , name="latest_photo" , methods={"GET"})
     * @throws ImagickException
     */
    public function getLatestPhotoUploadedName(Request $request) {
        $number = $this->imageRepository->getLastOwnedId();
        $response = $this->filenamesToRender;
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
        $this->filenamesToRender = array();
        return new JsonResponse($image->getFilename(), 200);


    }

    /**
     * @Route("/send/fullPhoto/{filename}", name="send_full_photo")
     */
    public function sendPhoto(string $filename)
    {
        /** @var Image $image */
        $image = $this->imageRepository->findOneBy(['originalName' => $filename]);

        if ($image->getOwner() === $this->security->getUser()) {
            $imagePath = $this->uploaderHelper->getFullPath($image->getOriginalName());
            $response = new BinaryFileResponse($imagePath);
            return $response;
        }
        return $this->json("Not authorized to view this photo", 401);

    }




}
