<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Famosos;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

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

    public function findByUser(User $user)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.createdBy = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Famosos[] Returns an array of Famosos objects
     */
    public function findNotDeleted(): array
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.eliminado = :val')
            ->setParameter('val', false)
            ->getQuery()
            ->getResult();
    }

    public function findByUserWithPagination(User $user, $start, $length, $search, $order)
    {
        return $this->getFilteredQuery($user, $search, $order)
            ->setFirstResult($start)
            ->setMaxResults($length)
            ->getQuery()
            ->getResult();
    }

    private function getFilteredQuery(User $user, $search, $order): QueryBuilder
    {
        $qb = $this->createQueryBuilder('f')
            ->where('f.createdBy = :user')
            ->setParameter('user', $user)
            ->andWhere('f.eliminado = :eliminado')
            ->setParameter('eliminado', false);

        if ($search) {
            $qb->andWhere('f.nombre LIKE :search OR f.apellido LIKE :search OR f.profesion LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        if (isset($order['column']) && isset($order['dir'])) {
            $columnsMap = [
                0 => 'f.id',
                1 => 'f.nombre',
                2 => 'f.apellido',
                3 => 'f.profesion',
            ];
            $orderColumn = $columnsMap[$order['column']] ?? 'f.id';
            $qb->orderBy($orderColumn, $order['dir']);
        }

        return $qb;
    }

    public function countNotDeletedByUser(User $user)
    {
        return $this->createQueryBuilder('f')
            ->select('COUNT(f.id)')
            ->where('f.createdBy = :user')
            ->andWhere('f.eliminado = :eliminado')
            ->setParameter('user', $user)
            ->setParameter('eliminado', false)
            ->getQuery()
            ->getSingleScalarResult();
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
