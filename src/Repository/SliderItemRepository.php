<?php

namespace App\Repository;

use App\Entity\SliderItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SliderItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method SliderItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method SliderItem[]    findAll()
 * @method SliderItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SliderItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SliderItem::class);
    }

    public function listPubAction()
    {
        return $this
            ->createQueryBuilder('c')
            ->andWhere('c.isPublish = :isPublish')
            ->setParameter('isPublish', true)
            ->getQuery()
            ->getResult();
    }

    public function listAction()
    {
        return $this->createQueryBuilder('c')

            ;
    }

}