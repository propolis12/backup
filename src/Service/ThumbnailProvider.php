<?php

namespace App\Service;

use App\Entity\Image;
use App\Repository\ImageRepository;
use Symfony\Component\Security\Core\Security;

class ThumbnailProvider {

    /**
     * @var ImageRepository
     */
    private ImageRepository $imageRepository;
    /**
     * @var Security
     */
    private Security $security;
    /**
     * @var UploaderHelper
     */
    private UploaderHelper $uploaderHelper;

    public function __construct(ImageRepository $imageRepository , Security $security, UploaderHelper $uploaderHelper)
    {
        $this->imageRepository = $imageRepository;
        $this->security = $security;
        $this->uploaderHelper = $uploaderHelper;
    }


    /**
     * @param string $originalName
     * @throws \ImagickException
     */
    function provideThumbnail(string $originalName, $columns, $rows) {
        $publicName = $this->uploaderHelper->getFullPath($originalName);
        $imagick = new \Imagick($publicName);
        $imagick->setbackgroundcolor('rgb(64, 64, 64)');
        $imagick->thumbnailImage($columns, $rows, false, false);
        header("Content-Type: image/jpg");
        /*$response = new BinaryFileResponse($imagick->getFilename());
        return $response;*/
        echo $imagick->getImageBlob();
    }

}