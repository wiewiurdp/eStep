<?php

declare(strict_types = 1);

namespace App\Service;

use App\Entity\Booking;
use App\Repository\AttendeeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;

class BookingService
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;
    /**
     * @var AttendeeRepository
     */
    private AttendeeRepository $attendeeRepository;

    /**
     * @var PresenceService
     */
    private PresenceService $presenceService;

    /**
     * @param EntityManagerInterface $entityManager
     * @param AttendeeRepository     $attendeeRepository
     */
    public function __construct(EntityManagerInterface $entityManager, AttendeeRepository $attendeeRepository, PresenceService $presenceService)
    {
        $this->entityManager = $entityManager;
        $this->attendeeRepository = $attendeeRepository;
        $this->presenceService = $presenceService;
    }

    /**
     * @param array $bookings
     * @param array $events
     */
    public function synchronizingBookingsWithEvents(array $bookings, array $events): void
    {
        /** @var \Google_Service_Calendar_Event $event */
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
        $booking = $this->updateAttendeesBasedOnEvent($event, $booking);

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
    public function processBooking(Booking $booking, $event): void
    {
        $booking->setGoogleId($event->getId());
        $this->presenceService->createPresences($booking);
        $this->entityManager->persist($booking);
        $this->entityManager->flush();
    }

    /**
     * @param Booking                         $booking
     * @param \Google_Service_Calendar_Events $events
     */
    public function processRecurrenceBookings(Booking $booking, \Google_Service_Calendar_Events $events): void
    {

        /** @var \Google_Service_Calendar_Event $item */
        foreach ($events->getItems() as $item) {
            if ($item->getStart()->getDateTime() == $booking->getStart()->format('Y-m-d\TH:i:sP')) {
                $booking->setGoogleId($item->getId());
                $this->presenceService->createPresences($booking);
            } else {
                $recurrenceBooking = new Booking();
                $recurrenceBooking->setGoogleId($item->getId());
                $recurrenceBooking->setStart(new \DateTime($item->getStart()->getDateTime()));
                $recurrenceBooking->setEnd(new \DateTime($item->getEnd()->getDateTime()));
                $recurrenceBooking->setSummary($booking->getSummary());
                $recurrenceBooking->setLocation($booking->getLocation());
                $recurrenceBooking->setRecurrence($booking->getRecurrence());
                $recurrenceBooking->setRecurrenceFinishedOn($booking->getRecurrenceFinishedOn());
                $recurrenceBooking->setLocation($booking->getLocation());
                $this->presenceService->createPresences($recurrenceBooking);
                $this->updateAttendeesBasedOnEvent($item, $recurrenceBooking);
                $this->entityManager->persist($recurrenceBooking);
            }
        }
        $this->entityManager->flush();
    }

    /**
     * @param \Google_Service_Calendar_Event $event
     * @param Booking                        $booking
     *
     * @return Booking
     */
    private function updateAttendeesBasedOnEvent(\Google_Service_Calendar_Event $event, Booking $booking): Booking
    {
        $attendeesEmails = null;
        $attendees = [];
        foreach ($event->getAttendees() as $attendee) {
            $attendeesEmails[] = $attendee->getEmail();
        }

        if ($attendeesEmails) {
            $attendees = $this->attendeeRepository->findBy(['mail' => $attendeesEmails]);
        }
        $attendeesCollection = new ArrayCollection($attendees);
        $booking->updateAttendees($attendeesCollection);

        return $booking;
    }

    /**
     * @param Booking $booking
     */
    public function transformAttendees(Booking $booking): void
    {

        if ($booking->getAttendeesJSON()) {
            $attendeesFromJSON = json_decode($booking->getAttendeesJSON(), true);
            foreach ($attendeesFromJSON as $item) {
                $attendeesIds[] = $item['id'];
            }
            $attendees = [];

            if (!empty($attendeesIds)) {
                $attendees = $this->attendeeRepository->findBy(['id' => $attendeesIds]);
            }
            $attendeesCollection = new ArrayCollection($attendees);
            $booking->updateAttendees($attendeesCollection);
        }
    }
}
