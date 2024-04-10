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

    public function findNotDeletedWithCriteria($start, $length, $search, $order)
    {
        $qb = $this->createQueryBuilder('f')
                   ->where('f.eliminado = :eliminado')
                   ->setParameter('eliminado', false);
    
        if ($search) {
            $qb->andWhere('f.nombre LIKE :search OR f.apellido LIKE :search OR f.profesion LIKE :search')
               ->setParameter('search', '%' . $search . '%');
        }
    
        // Mapeo de las columnas de DataTables a las propiedades de la entidad
    $columnsMap = [
        0 => 'f.id', // '0' es el índice de la columna para 'id'
        1 => 'f.nombre', // '1' es el índice de la columna para 'nombre'
        2 => 'f.apellido', // '2' es el indice de la columna para 'apellido'
        3 => 'f.profesion', // '3' es el índice de la columna para 'profesion'
    ];

    // los índices 'column' y 'dir' para definir el orden
    if(isset($order['column']) && isset($order['dir'])) {
        // con la estructura abajo se obtiene el campo de la entidad por el cual ordenar basado en el índice de la columna
        $orderColumn = array_key_exists($order['column'], $columnsMap) ? $columnsMap[$order['column']] : 'f.id';
        
        //pasar la ordenacion al constructor de la query
        $qb->orderBy($orderColumn, $order['dir']);
    }
    
        $qb->setFirstResult($start)->setMaxResults($length);
    
        return $qb->getQuery()->getResult();
    }
    
        public function countFilteredNotDeleted($search)
        {
            $qb = $this->createQueryBuilder('f')
                    ->select('COUNT(f.id)')
                    ->where('f.eliminado = :eliminado')
                    ->setParameter('eliminado', false);
    
            if ($search) {
                $qb->andWhere('f.nombre LIKE :search OR f.apellido LIKE :search OR f.profesion LIKE :search')
                ->setParameter('search', '%' . $search . '%');
            }
    
            return $qb->getQuery()->getSingleScalarResult();
        }
    
        public function countNotDeleted()
        {
            return $this->createQueryBuilder('f')
                        ->select('COUNT(f.id)')
                        ->where('f.eliminado = :eliminado')
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
