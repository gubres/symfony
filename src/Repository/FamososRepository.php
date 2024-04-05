<?php

namespace App\Repository;

use App\Entity\Famosos;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Famosos>
 *
 * @method Famosos|null find($id, $lockMode = null, $lockVersion = null)
 * @method Famosos|null findOneBy(array $criteria, array $orderBy = null)
 * @method Famosos[]    findAll()
 * @method Famosos[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FamososRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Famosos::class);
    }

    //    /**
    //     * @return Famosos[] Returns an array of Famosos objects
    //     */
    public function findNotDeleted(): array
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.eliminado = :val')
            ->setParameter('val', false)
            ->getQuery()
            ->getResult();
    }
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('f.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Famosos
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
