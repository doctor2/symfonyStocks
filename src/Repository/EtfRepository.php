<?php

namespace App\Repository;

use App\Entity\Etf;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class EtfRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Etf::class);
    }

    /**
     * @return string[]
     */
    public function findAllFigis(): array
    {
        $etfFigis = $this->createQueryBuilder('etf')
            ->select('etf.figi')
            ->getQuery()
            ->getResult();

        return array_column($etfFigis, 'figi');
    }

    public function save(Etf $etf): void
    {
        $this->getEntityManager()->persist($etf);
        $this->getEntityManager()->flush();
    }
}
