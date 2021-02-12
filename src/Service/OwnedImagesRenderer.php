<?php

namespace App\Service;

use App\Repository\ImageRepository;
use Symfony\Component\Security\Core\Security;

class OwnedImagesRenderer {


    /**
     * @var ImageRepository
     */
    private ImageRepository $imageRepository;
    /**
     * @var Security
     */
    private Security $security;

    public function __construct(ImageRepository $imageRepository , Security $security)
    {
        $this->imageRepository = $imageRepository;
        $this->security = $security;
    }




}