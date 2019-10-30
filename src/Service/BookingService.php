<?php

declare(strict_types = 1);

namespace App\Service;

use App\Entity\Booking;
use Doctrine\ORM\EntityManagerInterface;

class BookingService
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * BookingService constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function synchronizingBookingsWithEvents($bookings, $events): void
    {
        foreach ($events as $event) {
            $eventIds[] = $event->getId();
            $bookingFound = null;
            /** @var Booking $booking */
            foreach ($bookings as $key => $booking) {
                $bookingIds[$key] = $booking->getGoogleId();
                if ($booking->getGoogleId() === $event->getId()) {
                    $bookingFound = true;
                    if ($booking->getModifiedAt() < new \DateTime($event->getUpdated())) {
                        $updatedBooking = $this->settingAttributes($booking, $event);
                        $this->entityManager->persist($updatedBooking);
                        $this->entityManager->flush();
                    }
                }
            }
            if (!$bookingFound) {
                $newBooking = new Booking();
                $newBooking->setGoogleId($event->getId());
                $newBooking = $this->settingAttributes($newBooking, $event);
                $this->entityManager->persist($newBooking);
                $this->entityManager->flush();
            }
        }
        $this->removingNotExistingBookings($bookingIds, $eventIds, $bookings);

    }

    /**
     * @param Booking                        $booking
     * @param \Google_Service_Calendar_Event $event
     *
     * @return Booking
     */
    private function settingAttributes(Booking $booking, \Google_Service_Calendar_Event $event): Booking
    {
        if ($event->getStart()->dateTime) {
            $booking->setStart(new \DateTime($event->getStart()->dateTime));
        } else {
            $booking->setStart(new \DateTime($event->getStart()->date));
        }
        if ($event->getEnd()->dateTime) {
            $booking->setEnd(new \DateTime($event->getEnd()->dateTime));
        } else {
            $booking->setEnd(new \DateTime($event->getEnd()->date));
        }
        $booking->setSummary($event->getSummary());
        $booking->setDescription($event->getDescription());
        $booking->setLocation($event->getLocation());
        $booking->setRecurrence($event->getRecurrence());

        return $booking;
    }

    /**
     * @param array $bookingIds
     * @param array $eventIds
     * @param array $bookings
     */
    private function removingNotExistingBookings(array $bookingIds, array $eventIds, array $bookings): void
    {
        $notExistingBookings = array_diff($bookingIds, $eventIds);
        foreach ($notExistingBookings as $notExistingBooking) {
            foreach ($bookings as $booking) {
                if ($notExistingBooking === $booking->getGoogleId()) {
                    $this->entityManager->remove($booking);
                    $this->entityManager->flush();
                }
            }
        }
    }
}
