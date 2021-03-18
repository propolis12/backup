<?php

namespace App\Controller;

use App\Entity\Album;
use App\Entity\Image;
use App\Repository\AlbumRepository;
use App\Repository\ImageRepository;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class AlbumController extends AbstractController
{


    /**
     * @var Security
     */
    private Security $security;
    /**
     * @var AlbumRepository
     */
    private AlbumRepository $albumRepository;
    /**
     * @var ImageRepository
     */
    private ImageRepository $imageRepository;
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    public function __construct( Security $security , AlbumRepository $albumRepository , ImageRepository $imageRepository , EntityManagerInterface $entityManager)
    {


        $this->security = $security;
        $this->albumRepository = $albumRepository;
        $this->imageRepository = $imageRepository;
        $this->entityManager = $entityManager;
    }

    /**
     *@Route("/album/create", name="album_create", methods={"POST"})
     */
    public function create(Request $request) {
        $albumName = json_decode($request->getContent(),true);
        $album = new Album();
        $album->setOwner($this->security->getUser());
        $album->setName($albumName['albumName']);
        try {
            $this->entityManager->persist($album);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            return $this->json($errorMessage, 500);
        }

        //echo $albumName["albumName"];
        //dump($request);
        //print_r($albumName);
        return new JsonResponse( $albumName["albumName"],201);
    }


    /**
     * @Route("/fetch/albums" , name="fetch_albums", methods={"GET"})
     */
    public function provideAlbums() {
        $this->security->getUser()->getAlbums();
        return $this->json($this->security->getUser()->getAlbums(),200, [],[
            'groups' => ['main']
        ]) ;
    }


    /**
     * @Route("/fetch/album/images/{albumName}", name="fetch_album_images", methods={"GET"})
     */
    public function provideAlbumImages(string $albumName) {
        $albums = $this->getUser()->getAlbums();
        foreach ($albums as $album) {
            if($album->getName() === $albumName) {
                $albumImages = $album->getImage();
                return $this->json($albumImages,200,[],[
                    'groups' => ['image']
                ]);
            }
        }
        return $this->json("bad request", 400);
    }

    /**
     * @Route("/add/to/album/{albumName}", name="add_to_album" , methods={"POST"})
     */
    public function addToAlbum(string $albumName, Request $request) {

        $data = json_decode($request->getContent(),true);
        /** @var Image $image */
        $image = $this->imageRepository->findOneBy(['originalName' => $data["filename"] ]);
        /** @var Album $album */
        $album = $this->albumRepository->findOneBy(['name'=> $albumName]);
        if (($this->security->getUser() === $image->getOwner()) && ($this->security->getUser() === $image->getOwner())) {

            if ($album->addImage($image)) {
                $this->entityManager->persist($album);
                $this->entityManager->flush();

                return $this->json($data["filename"] . "successfully added ", 201);
            }
        }
        return $this->json("operation was not successful",500);

    }/**
     * @Route("/remove/from/album/{albumName}", name="remove_from_album" , methods={"POST"})
     */
    public function removeFromAlbum(string $albumName, Request $request ) {

        $data = json_decode($request->getContent(),true);
        /** @var Image $image */
        $image = $this->imageRepository->findOneBy(['originalName' => $data["filename"] ]);
        /** @var Album $album */
        $album = $this->albumRepository->findOneBy(['name'=> $albumName]);
        if (($this->security->getUser() === $image->getOwner()) && ($this->security->getUser() === $image->getOwner())) {

            foreach ($album->getImage() as $currentImage) {
                if ($currentImage->getOriginalName() === $image->getOriginalName())  {
                    $album->removeImage($currentImage);
                    $this->entityManager->persist($album);
                    $this->entityManager->flush();
                    return $this->json($data["filename"]. "was deleted from album " . $album->getName(), 200);
                }
            }
        }
        return $this->json("operation was not successful",500);

    }


    /**
     * @param string $name
     * @Route("/delete/album/{name}" , name="delete_album" , methods={"DELETE"})
     */
    public function deleteAlbum(string $name) {
        $album = $this->albumRepository->findOneBy(['name' => $name]);
        if ($this->security->getUser() === $album->getOwner()) {
            $this->security->getUser()->removeAlbum($album);
            $this->entityManager->persist($this->security->getUser());
            $this->entityManager->flush();
            return $this->json("deleted successfully",204);
        }
        return $this->json("not authorized to delete this album" , 401);



    }
}
