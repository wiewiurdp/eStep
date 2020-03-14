<?php

declare(strict_types = 1);

namespace App\Service;

use App\Entity\Booking;
use App\Entity\Presence;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;

class PresenceService
{

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var GoogleCalendarService
     */
    private GoogleCalendarService $googleCalendarService;

    /**
     * PresenceService constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param GoogleCalendarService  $googleCalendarService
     */
    public function __construct(EntityManagerInterface $entityManager, GoogleCalendarService $googleCalendarService)
    {
        $this->entityManager = $entityManager;
        $this->googleCalendarService = $googleCalendarService;
    }
    /**
     * @param EntityManagerInterface $entityManager
     */

    /**
     * @param Booking $booking
     */
    public function createPresences(Booking $booking): void
    {

        foreach ($booking->getAttendees() as $attendee) {
            $presence = $this->initiatePresence($attendee);
            $booking->addPresence($presence);
        }
    }

    /**
     * @param Booking $booking
     */
    public function updatePresences(Booking $booking): void
    {
        $newAttendees = $booking->getAttendees();
        $presences = $booking->getPresences();
        $attendees = new ArrayCollection();
        foreach ($presences as $presence) {
            $attendees->add($presence->getAttendee());
        }
        foreach ($presences as $presence) {
            if (!$newAttendees->contains($presence->getAttendee())) {
                $booking->removePresence($presence);
            }
        }
        foreach ($newAttendees as $attendee) {
            if (!$attendees->contains($attendee)) {
                $newPresence = $this->initiatePresence($attendee);
                $booking->addPresence($newPresence);
            }
        }
    }

    /**
     * @param Booking $booking
     */
    public function synchronizePresenceResponseStatus(Booking $booking): void
    {
        $event = $this->googleCalendarService->getEvent($booking->getGoogleId());
        $presences = $booking->getPresences();
        /** @var \Google_Service_Calendar_EventAttendee $attendee */
        foreach ($event->getAttendees() as $attendee) {
            foreach ($presences as $presence) {
                if ($attendee->getEmail() === $presence->getAttendee()->getMail()) {
                    $presence->setStated($attendee->getResponseStatus());
                    $this->entityManager->persist($presence);
                    $this->entityManager->flush();
                }
            }
        }
    }

    private function initiatePresence($attendee)
    {
        $presence = new Presence();
        $presence->setAttendee($attendee);
        $presence->setStated(Presence::INITIAL_STATED_VALUE);

        return $presence;
    }
}
