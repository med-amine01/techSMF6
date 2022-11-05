<?php

namespace App\Repository;

use App\Entity\Personne;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Personne>
 *
 * @method Personne|null find($id, $lockMode = null, $lockVersion = null)
 * @method Personne|null findOneBy(array $criteria, array $orderBy = null)
 * @method Personne[]    findAll()
 * @method Personne[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PersonneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Personne::class);
    }

    public function save(Personne $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Personne $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Personne[] Returns an array of Personne objects
     */
    public function findByPersonByAge($min,$max)
    {
        $query =  $this->createQueryBuilder('p');

        $this->addInterval($query,$min,$max);

        return $query->orderBy('p.id', 'ASC')->getQuery()->getResult();
    }


    public function statsPersonByAgeIntervale($min,$max)
    {
        $qb =  $this->createQueryBuilder('p')
            ->select('avg(p.age) as ageMoyen, count(p.id) as numberPersonne');

        $this->addInterval($qb,$min,$max);

        return $qb->getQuery()->getResult();
    }

    private function addInterval(QueryBuilder $qb,$min,$max)
    {
        $qb->andWhere('p.age >= :min and p.age <= :max')
            ->setParameters(['min'=>$min , 'max'=>$max]);
    }

//    public function findOneBySomeField($value): ?Personne
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
