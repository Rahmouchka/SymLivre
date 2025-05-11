<?php

namespace App\Repository;

use App\Entity\Livre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Livre>
 */
class LivreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Livre::class);
    }
    /**
     * Recherche des livres en fonction d'un terme de recherche
     *
     * @param string|null $searchTerm
     * @return \Doctrine\ORM\Query
     */
    public function findBySearchQuery(?string $searchTerm)
    {
        $queryBuilder = $this->createQueryBuilder('l');
        //QueryBuilder est un outil de Doctrine pour construire dynamiquement des requêtes SQL.
        if ($searchTerm) {
            $queryBuilder
                ->where('l.titre LIKE :searchTerm')
                ->orWhere('l.editeur LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }

        return $queryBuilder->orderBy('l.id', 'DESC')->getQuery();
    }
    //    /**
    //     * @return Livre[] Returns an array of Livre objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('l.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Livre
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
