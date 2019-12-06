<?php

namespace App\Repository;

use App\Entity\Booking;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method Booking|null find($id, $lockMode = null, $lockVersion = null)
 * @method Booking|null findOneBy(array $criteria, array $orderBy = null)
 * @method Booking[]    findAll()
 * @method Booking[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookingRepository extends ServiceEntityRepository
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Booking::class);
    }

    /**
     * @param \DateTimeInterface $start
     * @param \DateTimeInterface $end
     *
     * @return mixed
     */
    public function findBetweenDates(\DateTimeInterface $start, \DateTimeInterface $end)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.start BETWEEN :start AND :end OR b.end BETWEEN :start AND :end')
            ->setParameter('start', $start->format('Y-m-d H:i:s'))
            ->setParameter('end', $end->format('Y-m-d H:i:s'))
            ->getQuery()
            ->getResult();
    }
}
