<?php

namespace App\Service;

use App\Entity\Image;
use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\FilesystemInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\File;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\Security;
use function Symfony\Component\String\s;

class UploaderHelper
{

    /** to public path */
    const IMAGE_DIRECTORY = '/var/uploadedPhotos';


    /**
     * @var KernelInterface
     */
    private KernelInterface $kernel;
    /**
     * @var Security
     */
    private Security $security;
    /**
     * @var FilesystemInterface
     */
    private FilesystemInterface $filesystem;

    /**
     * @var EntityManagerInterface
     */

    public function __construct(FilesystemInterface $uploadFilesystem, LoggerInterface $logger,  KernelInterface $kernel, Security $security )
    {

        $this->logger = $logger;
        $this->kernel = $kernel;
        $this->security = $security;

        $this->filesystem = $uploadFilesystem;
    }






    public function uploadFile(File $uploadedFile): string
    {
        /*/** @var Image $image */
        //$image = new Image();
        if ($uploadedFile instanceof UploadedFile) {
            $originalFilename= $uploadedFile->getClientOriginalName();

        } else {
            $originalFilename = $uploadedFile->getFilename();
        }
        //$destination = $this->kernel->getProjectDir().'/public/photos';
        $newFilename = pathinfo($originalFilename, PATHINFO_FILENAME).'-'.uniqid().'.'.$uploadedFile->guessExtension();
        //dd($newFilename);
        $stream = fopen($uploadedFile->getPathname(), 'r');
        $result = $this->filesystem->writeStream(
            $newFilename,
            $stream
        );

        if (is_resource($stream)) {
            fclose($stream);
        }

        if ($result === false) {
            throw new \Exception(sprintf('Could not write uploaded file "%s"', $newFilename));
        }
        //$uploadedFile->move($destination, $newFilename);

       /* $stream = fopen($uploadedFile->getPathname(), 'r');
        $this->privateFileSystem->writeStream( self::IMAGE_DIRECTORY.'/'.$newFilename, $stream);*/
        //$uploadedFilename = $uploaderHelper->uploadFile($uploadedFile);
        /*if (is_resource($stream)) {
            fclose($stream);
        }*/


       /* $stream = fopen($file->getPathname(), 'r');
        $result = $this->filesystem->writeStream(
            $directory.'/'.$newFilename,
            $stream,
            [
                'visibility' => $isPublic ? AdapterInterface::VISIBILITY_PUBLIC : AdapterInterface::VISIBILITY_PRIVATE
            ]
        );

        if ($result === false) {
            throw new \Exception(sprintf('Could not write uploaded file "%s"', $newFilename));
        }

        if (is_resource($stream)) {
            fclose($stream);
        }*/

        return $newFilename;
    }


    public function getFullPath(string $filename): string
    {
         return $this->kernel->getProjectDir().UploaderHelper::IMAGE_DIRECTORY.'/'.$filename;
    }
}
