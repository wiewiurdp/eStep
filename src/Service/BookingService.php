<?php

declare(strict_types = 1);

namespace App\Service;

use App\Entity\Booking;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class BookingService
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var LoggerInterface
     */
    private $logger;

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
                    $eventCreated = new \DateTime($event->getCreated());
                    $eventUpdated = new \DateTime($event->getUpdated());
                    if ($eventCreated->format('Y-m-d H:i:s') < $eventUpdated->format('Y-m-d H:i:s')) {
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

    /**
     * @param Booking $booking
     * @param         $event
     */
    public function saveBooking(Booking $booking, $event): void
    {
        $booking->setGoogleId($event->getId());
        $this->entityManager->persist($booking);
        $this->entityManager->flush();
    }

    /**
     * @param Booking                         $booking
     * @param \Google_Service_Calendar_Events $events
     */
    public function saveRecurrenceBookings(Booking $booking, \Google_Service_Calendar_Events $events):void
    {
        /** @var \Google_Service_Calendar_Event $item */
        foreach ($events->getItems() as $item) {
            $recurrenceBooking = clone $booking;
            $recurrenceBooking->setGoogleId($item->getId());
            $recurrenceBooking->setStart(new \DateTime($item->getStart()->getDateTime()));
            $recurrenceBooking->setEnd(new \DateTime($item->getEnd()->getDateTime()));
            $this->entityManager->persist($recurrenceBooking);
        }
        $this->entityManager->flush();
    }
}
