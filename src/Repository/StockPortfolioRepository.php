<?php

namespace App\Repository;

use App\Entity\StockPortfolio;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StockPortfolio>
 *
 * @method StockPortfolio|null find($id, $lockMode = null, $lockVersion = null)
 * @method StockPortfolio|null findOneBy(array $criteria, array $orderBy = null)
 * @method StockPortfolio[]    findAll()
 * @method StockPortfolio[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StockPortfolioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StockPortfolio::class);
    }

//    /**
//     * @return StockPortfolio[] Returns an array of StockPortfolio objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?StockPortfolio
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
