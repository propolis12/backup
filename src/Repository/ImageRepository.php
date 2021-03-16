<?php

namespace App\Repository;

use App\Entity\Image;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;

/**
 * @method Image|null find($id, $lockMode = null, $lockVersion = null)
 * @method Image|null findOneBy(array $criteria, array $orderBy = null)
 * @method Image[]    findAll()
 * @method Image[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImageRepository extends ServiceEntityRepository
{
    /**
     * @var Security
     */
    private Security $security;
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $manager;

    public function __construct(ManagerRegistry $registry, Security $security, EntityManagerInterface $manager)
    {
        parent::__construct($registry, Image::class);
        $this->security = $security;
        $this->manager = $manager;
    }


    /**
     * @param string|null $orderByColumn
     * @param string|null $direction
     * @return string[] Returns array of owned images filenames
     */
    public function getOwnedImagesFilenames(?string $orderByColumn, ?string $direction): array
    {
            return $this->createQueryBuilder('r')
            ->select('r.originalName, r.latitude, r.longitude, r.UploadedAt')
            ->andWhere('r.owner = :val' )
            ->setParameter('val' , $this->security->getUser())
            ->orderBy($orderByColumn ?: 'r.UploadedAt', $direction ?: "ASC" )
            ->getQuery()
            ->getResult()
        ;
         $returnArray = array();
        foreach ($entities as $entity) {
        $returnArray[get_class($entity)] = $entity;
        }
    return $returnArray;

    }

    public function getLatestPhoto() {
        return $this->createQueryBuilder('q')
            ->select('q.filename')
            ->join('q.image', 'nq')





            ->from('image','i')
                ->select('MAX(i.UploadedAt) as max_date')
                ->andWhere('q.owner = :val' )
                ->setParameter('val', $this->security->getUser())




            ->getQuery()
            ->getResult();

            //->groupBy('q.filename');
            //->andHaving(max('q.UploadedAt'));




    }

    public function getLastUploaded() {
        $rsm = new ResultSetMapping();
        $query = $this->manager->createNativeQuery('SELECT filename from  image ', $rsm);
        return $query->getResult();
    }


    public function getLastOwnedId() {
        return  $this->createQueryBuilder('r')
            ->select('max(r.id)')
            ->andWhere('r.owner = :val' )
            ->setParameter('val', $this->security->getUser())
            ->getQuery()
            ->getResult();

    }


    public function getPublicImages() {
        return $this->createQueryBuilder('r')
            ->select('r.originalName, r.latitude, r.longitude, r.UploadedAt , r.publishedAt  , o.username ')
            ->join('r.owner', 'o')
            //->join('r.likes', 'l')
            ->andWhere('r.public = :val' )
            ->setParameter('val' , 1)
            //->orderBy($orderByColumn ?: 'r.UploadedAt', $direction ?: "ASC" )
            ->getQuery()
            ->getResult();
    }

    public function getPublicImages2() {
         $result = $this->findBy(['public' => 1]);
         return array($result);
    }

    public function getImageLikes($filename) {
        return $this->createQueryBuilder('l')
            ->select(' o.username')
            ->join('l.likes', 'j')
            ->join('l.owner', 'o')
            ->andWhere('l.originalName = :val')
            ->setParameter('val' , $filename)
            ->getQuery()
            ->getResult();

    }

    public function getLikedImages() {
        return $this->createQueryBuilder('c')
            ->select('c.originalName, c.latitude, c.longitude, c.UploadedAt , c.publishedAt')
            ->join('c.users', 'u')
            ->andWhere('u.username = :val')
            ->setParameter('val', $this->security->getUser()->getUsername())
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Image[] Returns an array of Image objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Image
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
