<?php

namespace App\Repository;

use App\Entity\OrderTransaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method OrderTransaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrderTransaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrderTransaction[]    findAll()
 * @method OrderTransaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderTransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderTransaction::class);
    }

    // /**
    //  * @return OrderTransaction[] Returns an array of OrderTransaction objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?OrderTransaction
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
