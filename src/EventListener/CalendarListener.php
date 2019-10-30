<?php

/**
 * Copyright © Bold Brand Commerce Sp. z o.o. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types = 1);

namespace App\EventListener;

use App\Entity\Booking;
use App\Service\BookingService;
use App\Service\GoogleCalendarService;
use App\Repository\BookingRepository;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use CalendarBundle\Entity\Event;
use CalendarBundle\Event\CalendarEvent;

class CalendarListener
{
    /**
     * @var BookingRepository
     */
    private $bookingRepository;

    /**
     * @var UrlGeneratorInterface
     */
    private $router;

    /**
     * @var GoogleCalendarService
     */
    private $googleCalendarService;

    /**
     * @var BookingService
     */
    private $bookingService;

    public function __construct(
        BookingRepository $bookingRepository,
        UrlGeneratorInterface $router,
        GoogleCalendarService $googleCalendarService,
        BookingService $bookingService
    )
    {
        $this->bookingRepository = $bookingRepository;
        $this->router = $router;
        $this->googleCalendarService = $googleCalendarService;
        $this->bookingService = $bookingService;
    }

    /**
     * @param CalendarEvent $calendar
     */
    public function load(CalendarEvent $calendar): void
    {
        $start = $calendar->getStart();
        $end = $calendar->getEnd();
        // Modify the query to fit to your entity and needs
        // Change booking.beginAt by your start date property
        $events = $this->googleCalendarService->getEventsBetweenDates($start, $end);
        $bookingsToUpdate = $this->bookingRepository->findBetweenDates($start, $end);
        $this->bookingService->synchronizingBookingsWithEvents($bookingsToUpdate, $events);
        $bookings = $this->bookingRepository->findBetweenDates($start, $end);
        /** @var Booking $booking */
        foreach ($bookings as $booking) {
            // this create the events with your data (here booking data) to fill calendar
            $bookingEvent = new Event(
                $booking->getSummary(),
                $booking->getStart(),
                $booking->getEnd() // If the end date is null or not defined, a all day event is created.
            );
            $bookingEvent->setOptions([
                'backgroundColor' => 'blue',
                'borderColor' => 'green',
            ]);
            $bookingEvent->addOption(
                'url',
                $this->router->generate('booking_show', [
                    'id' => $booking->getId(),
                ])
            );

            // finally, add the event to the CalendarEvent to fill the calendar
            $calendar->addEvent($bookingEvent);
        }
    }
}
