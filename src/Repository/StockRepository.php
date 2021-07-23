<?php

namespace App\Repository;

use App\Entity\Stock;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class StockRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Stock::class);
    }

    /**
     * @return string[]
     */
    public function findAllFigis(): array
    {
        $stockFigis = $this->createQueryBuilder('stock')
            ->select('stock.figi')
            ->getQuery()
            ->getResult();

        return array_column($stockFigis, 'figi');
    }

    /**
     * @return string[]
     */
    public function findAllCountries(): array
    {
        $stockFigis = $this->createQueryBuilder('stock')
            ->select('DISTINCT stock.country')
            ->where('stock.country IS NOT NULL')
            ->getQuery()
            ->getResult();

        return array_column($stockFigis, 'country');
    }

        /**
     * @return string[]
     */
    public function findAllSectors(): array
    {
        $stockFigis = $this->createQueryBuilder('stock')
            ->select('DISTINCT stock.sector')
            ->where('stock.sector IS NOT NULL')
            ->getQuery()
            ->getResult();

        return array_column($stockFigis, 'sector');
    }

    public function save(Stock $stock): void
    {
        $this->getEntityManager()->persist($stock);
        $this->getEntityManager()->flush();
    }

    // /**
    //  * @return Stock[] Returns an array of Stock objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Stock
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
