<?php

namespace App\Repository;

use App\Entity\Image;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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

    public function __construct(ManagerRegistry $registry, Security $security)
    {
        parent::__construct($registry, Image::class);
        $this->security = $security;
    }


    /**
     * @param string|null $orderByColumn
     * @param string|null $direction
     * @return string[] Returns array of owned images filenames
     */
    public function getOwnedImagesFilenames(?string $orderByColumn, ?string $direction): array
    {
        return  $this->createQueryBuilder('r')
            ->select('r.filename')
            ->andWhere('r.owner = :val' )
            ->setParameter('val' , $this->security->getUser())
            ->orderBy($orderByColumn ?: 'uploadedAt', $direction ?: "DESC" )
            ->getQuery()
            ->getResult()
        ;

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
