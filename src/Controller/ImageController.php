<?php

namespace App\Controller;

use App\Entity\Image;
use App\Form\ImageUpdateType;
use App\Repository\ImageRepository;
use App\Service\SharingToggler;
use App\Service\ThumbnailProvider;
use App\Service\UploaderHelper;
use Doctrine\ORM\EntityManagerInterface;
use ImagickException;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use phpDocumentor\Reflection\Types\This;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Mime\MimeTypes;

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
    /**
     * @var SharingToggler
     */
    private SharingToggler $sharingToggler;
    /**
     * @var ThumbnailProvider
     */
    private ThumbnailProvider $thumbnailProvider;


    public function __construct(Security $security , ImageRepository $imageRepository , UploaderHelper $uploaderHelper, LoggerInterface $logger , SharingToggler $sharingToggler,
                                ThumbnailProvider $thumbnailProvider) {
        $this->security = $security;
        $this->imageRepository = $imageRepository;
        $this->uploaderHelper = $uploaderHelper;
        $this->filenamesToRender = array();
        $this->logger = $logger;
        $this->sharingToggler = $sharingToggler;
        $this->thumbnailProvider = $thumbnailProvider;
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
            $imagick->thumbnailImage(300, 200, false, false);
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
            $this->thumbnailProvider->provideThumbnail($filename,300,200);
            /*$publicName = $this->uploaderHelper->getFullPath($filename);
            $imagick = new \Imagick($publicName);
            $imagick->setbackgroundcolor('rgb(64, 64, 64)');
            $imagick->thumbnailImage(300, 200, false, false);
            header("Content-Type: image/jpg");
            /*$response = new BinaryFileResponse($imagick->getFilename());
            return $response;*/
            /*echo $imagick->getImageBlob();*/
        }
    }


    /**
     * @Route("/public/photo/{filename}", name="send_public_thumbnail", methods={"GET"} )
     *
     * @param string $filename
     * @throws ImagickException
     */

    public function thumbnailPublicImage(string $filename) {
        $image = $this->imageRepository->findOneBy(['originalName' => $filename]);

            $this->thumbnailProvider->provideThumbnail($filename,300,200);
            /*$publicName = $this->uploaderHelper->getFullPath($filename);
            $imagick = new \Imagick($publicName);
            $imagick->setbackgroundcolor('rgb(64, 64, 64)');
            $imagick->thumbnailImage(300, 200, false, false);
            header("Content-Type: image/jpg");
            /*$response = new BinaryFileResponse($imagick->getFilename());
            return $response;*/
            /*echo $imagick->getImageBlob();*/
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
        $albumName = $request->request->get('data');
        //$albumName = $request->request->get('hiddenDropzoneInput');
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
        $mimeTypes = new MimeTypes();
        $mimeType = $mimeTypes->guessMimeType( $uploaderHelper->getFullPath($uploadedFile->getClientOriginalName()));
         array_push($this->filenamesToRender, $newFilename);
        $image->setPublic(false);
        $image->setMimetype($mimeType);
        $image->setFilename($newFilename);
        $image->setOwner($security->getUser());
        $image->setUploadedAt(new \DateTimeImmutable("now"));
        $originalFilename = $uploadedFile->getClientOriginalName();
        $clientNameExtension = pathinfo($originalFilename, PATHINFO_EXTENSION);
        if ($uploadedFile->guessExtension() === 'jpg' || $uploadedFile->guessExtension() === 'png' || $uploadedFile->guessExtension() === 'gif') {
            $newFilename = $originalFilename;
        } else {
            $newFilename = pathinfo($originalFilename, PATHINFO_FILENAME).".".$uploadedFile->guessExtension();
        }
        $image->setOriginalName($newFilename);
        if($albumName !== '') {
            $ownedAlbums = $this->getUser()->getAlbums();
            foreach ($ownedAlbums as $album) {
                if ($album->getName() === $albumName) {
                    $album->addImage($image);
                }
            }
        }
        $entityManager->persist($image);
        $entityManager->flush();
        return new JsonResponse($albumName, 201);

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
        if($image->getPublic()) {
            $imagePath = $this->uploaderHelper->getFullPath($image->getOriginalName());
            $response = new BinaryFileResponse($imagePath);
            return $response;
        }
        if ($image->getOwner() === $this->security->getUser()) {
            $imagePath = $this->uploaderHelper->getFullPath($image->getOriginalName());
            $response = new BinaryFileResponse($imagePath);
            return $response;
        }
        return $this->json("Not authorized to view this photo", 401);

    }


    /**
     * @Route("/delete/image/{filename}", name="delete_image" , methods={"DELETE"})
     */
    public function deleteImage(Request $request, string $filename, EntityManagerInterface $entityManager) {
         /** @var Image $image */
        $image = $this->imageRepository->findOneBy(['originalName' => $filename]);
        if($image->getOwner() === $this->security->getUser()) {
            $entityManager->remove($image);
            $entityManager->flush();
            $this->uploaderHelper->deleteFromSystem($filename);
            return $this->json($filename . " was deleted ", 204);

        }
        return $this->json("Not authorized to delete some of the  files", 401);

    }


    /**
     * @Route("/get/images" , name="get_images")
     */
    public function getImages() {
        $images = $this->security->getUser()->getImages();
        return $this->json($images, 200 ,[],[
            'groups' => ['image']
        ]);
    }


    /**
     * @Route("/get/image/info/{filename}" , name="get_image_info" , methods={"GET"})
     */
    public function getimageInfo(string $filename, Request $request) {
        /** @var Image $image */
        $image = $this->imageRepository->findOneBy(['originalName' => $filename]);
        if ($image->getOwner() === $this->security->getUser()) {
            return $this->json($image, 200, [], [
                'groups' => ['image']
            ]);
        } else {
            return $this->json("Not authorized to delete some of the  files", 401);
        }

    }

    /**
     * @param string $filename
     * @param Request $request
     * @param bool $public
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     * @Route("/make/public/{filename}", name="make_public", methods={"POST"})
     */
    public function makePublic(string $filename ) {
        if ($this->sharingToggler->toggleShare($filename,true)) {
            return $this->json("photo is shared now", 201);
        } else {
            return $this->json("Not authorized to manipulate some of the  files", 401);
        }
    }

    /**
     * @Route("/make/private/{filename}" , name="make_private" , methods={"POST"})
     */
    public function makePrivate(string $filename, Request $request, EntityManagerInterface $entityManager) {
        if ($this->sharingToggler->toggleShare($filename,false)) {
            return $this->json("photo is private now", 201);
        } else {
            return $this->json("Not authorized to manipulate some of the  files", 401);
        }
    }


    /**
     * @Route("/download/image/{filename}" , name="download_image" , methods={"GET"})
     */
    public function downloadImage(string $filename) {
        /** @var Image $image */
        $image = $this->imageRepository->findOneBy(['originalName' => $filename]);
        if ($image->getOwner() === $this->security->getUser()) {
                $response = new StreamedResponse(function() use ($filename) {
                $outputStream = fopen('php://output', 'wb');
                $filestream = $this->uploaderHelper->readStream($filename);
                stream_copy_to_stream($filestream , $outputStream);
            });
            $response->headers->set('Content-Type' , $image->getMimetype());
            $disposition = HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_ATTACHMENT,
            $image->getFilename()
            );
            //dd($disposition);
            $response->headers->set('Content-Disposition', $disposition);
            return $response;
        }
        return $this->json("Not authorized to delete this file ", 401);
    }


}
