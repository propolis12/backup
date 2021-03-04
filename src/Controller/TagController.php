<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Tag;
use App\Repository\ImageRepository;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class TagController extends AbstractController
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
     * @var TagRepository
     */
    private TagRepository $tagRepository;

    public function __construct(Security $security , ImageRepository $imageRepository, TagRepository $tagRepository) {

        $this->security = $security;
        $this->imageRepository = $imageRepository;
        $this->tagRepository = $tagRepository;
    }



    /**
     * @Route("/add/tags/{filename}", name="add_tags")
     */
    public function addTag(string $filename , Request $request, EntityManagerInterface $entityManager )
    {

        /** @var Image $image */
        $image = $this->imageRepository->findOneBy(['originalName' => $filename]);
        if (!($this->security->getUser() === $image->getOwner())) {
            return $this->json("Not authorized", 401);
        }


        $data = json_decode($request->getContent(),true);
        $tags = $data["tags"];
        $tagsInDatabase = $this->tagRepository->findAll();
        foreach($tags as $tag) {
            $found = false;
            foreach ($tagsInDatabase as $dtag) {
                if ($tag === $dtag->getName()) {
                    $image->addTag($dtag);
                    $entityManager->persist($image);
                    $entityManager->flush();
                    $found = true;
                    break;
                }
            }
            if(!$found) {
                $newtag = new Tag();
                $newtag->setName($tag);
                $image->addTag($newtag);
                $entityManager->persist($newtag);
                $entityManager->persist($image);
                $entityManager->flush();
            }
            $found = false;
        }
        return $this->json("tags were added", 201);
    }
}
