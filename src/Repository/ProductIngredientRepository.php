<?php

namespace App\Repository;

use App\Entity\ProductIngredient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ProductIngredient|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductIngredient|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductIngredient[]    findAll()
 * @method ProductIngredient[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductIngredientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductIngredient::class);
    }

    // /**
    //  * @return ProductIngredient[] Returns an array of ProductIngredient objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ProductIngredient
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
