<?php

namespace App\Repository;

use App\Entity\TokenHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TokenHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method TokenHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method TokenHistory[]    findAll()
 * @method TokenHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TokenHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TokenHistory::class);
    }

    // /**
    //  * @return TokenHistory[] Returns an array of TokenHistory objects
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
    public function findOneBySomeField($value): ?TokenHistory
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
