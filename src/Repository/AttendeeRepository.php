<?php

namespace App\Repository;

use App\Entity\Attendee;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Attendee|null find($id, $lockMode = null, $lockVersion = null)
 * @method Attendee|null findOneBy(array $criteria, array $orderBy = null)
 * @method Attendee[]    findAll()
 * @method Attendee[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AttendeeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Attendee::class);
    }

    /**
     * @param $batchId
     *
     * @return array
     */
    public function getAttendeesByBatchId($batchId): array
    {
        return $this->createQueryBuilder('attendees')
            ->select('attendees', 'attendees_roles', 'attendees_batches')
            ->join('attendees.batches', 'attendees_batches')
            ->andWhere('attendees_batches.id = :batchId')
            ->setParameter('batchId', $batchId)
            ->leftJoin('attendees.roles', 'attendees_roles')
            ->getQuery()
            ->getResult();
    }

    public function getAttendeesByBookingId($bookingId): array
    {
        return $this->createQueryBuilder('attendees')
            ->select('attendees.name', 'attendees.surname')
            ->join('attendees.bookings', 'attendees_bookings')
            ->andWhere('attendees_bookings.id = :bookingId')
            ->setParameter('bookingId', $bookingId)
            ->getQuery()
            ->getResult();
    }
    /*
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
