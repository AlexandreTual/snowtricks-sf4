<?php

namespace App\Repository;

use App\Entity\Timestampable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Timestampable|null find($id, $lockMode = null, $lockVersion = null)
 * @method Timestampable|null findOneBy(array $criteria, array $orderBy = null)
 * @method Timestampable[]    findAll()
 * @method Timestampable[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TimestampableRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Timestampable::class);
    }

    // /**
    //  * @return Timestampable[] Returns an array of Timestampable objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Timestampable
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
