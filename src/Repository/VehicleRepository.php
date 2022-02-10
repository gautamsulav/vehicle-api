<?php

namespace App\Repository;

use App\Entity\Vehicle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Vehicle|null find($id, $lockMode = null, $lockVersion = null)
 * @method Vehicle|null findOneBy(array $criteria, array $orderBy = null)
 * @method Vehicle[]    findAll()
 * @method Vehicle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VehicleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vehicle::class);
    }

    /**
     * @param $type
     * @return Query
     */
    public function findAllNotDeleted($type, $sort='id', $search = []): Query
    {
        $queryBuilder =  $this->createQueryBuilder('v')
            ->andWhere('v.deleted = :val')
            ->andWhere('v.type = :val2')
            ->setParameter('val', false)
            ->setParameter('val2', $type)
        ;

        if(!empty($search)) {
            foreach($search as $key=>$value) {
                $queryBuilder->andWhere('v.'.$key.'=:'.$key)->setParameter($key, $value);
            }
        }

        return $queryBuilder->orderBy('v.'.$sort, 'ASC')->getQuery();
    }

    function getOrFail(int $id)
    {
        $vehicle = $this->find($id);
        if (!$vehicle) {
            throw RecordNotFoundException('No vehicle found for id '.$id);
        }
    }
}
