<?php


namespace App\Service;

use App\Entity\Image;
use App\Repository\ImageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;







class SharingToggler {


    /**
     * @var ImageRepository
     */
    private ImageRepository $repository;
    /**
     * @var Security
     */
    private Security $security;
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    public function __construct(ImageRepository $repository , Security $security , EntityManagerInterface $entityManager)
   {
       $this->repository = $repository;
       $this->security = $security;
       $this->entityManager = $entityManager;
   }


   public function toggleShare(string $filename , bool $public) {
       /** @var Image $image */
       $image = $this->repository->findOneBy(['originalName' => $filename]);
       if ($image->getOwner() === $this->security->getUser()) {
           $image->setPublic($public);
           if ($public === true) {
               $image->setPublishedAt(new \DateTime('now'));
           } else if ($public === false) {
               $image->setPublishedAt(null);
           }
           $this->entityManager->persist($image);
           $this->entityManager->flush();
           return true;
       }
           return false;

   }


}