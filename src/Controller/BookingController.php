<?php

declare(strict_types = 1);

namespace App\Controller;

use App\Service\BookingService;
use App\Service\GoogleCalendarService;
use App\Entity\Booking;
use App\Form\BookingType;
use App\Repository\BookingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/booking")
 */
class BookingController extends AbstractController
{
    private $googleCalendarService;
    /**
     * @var BookingService
     */
    private $bookingService;

    /**
     * @param GoogleCalendarService $googleCalendarService
     * @param BookingService        $bookingService
     */
    public function __construct(GoogleCalendarService $googleCalendarService, BookingService $bookingService)
    {

        $this->googleCalendarService = $googleCalendarService;
        $this->bookingService = $bookingService;
    }

    /**
     * @Route("/calendar", name="booking_calendar", methods={"GET"})
     */
    public function calendar(BookingRepository $bookingRepository): Response
    {
        $client = $this->googleCalendarService->getClient();

        if (null === $client) {
            return $this->redirect($this->googleCalendarService->getAuthUrl());
        }
        return $this->render('booking/calendar.html.twig');
    }

    /**
     * @Route("/code", name="booking_code", methods={"GET"})
     */
    public function code(Request $request): Response
    {
        if ($request->query->get('code')) {
            $this->googleCalendarService->getClient($request->query->get('code'));
        } else {
            $this->googleCalendarService->getClient();
        }

        return $this->redirectToRoute('booking_calendar');
    }

    /**
     * @Route("/", name="booking_index", methods={"GET"})
     */
    public function index(Request $request, BookingRepository $bookingRepository): Response
    {
        if (!$this->googleCalendarService->getAccessToken()) {
            return $this->redirect($this->googleCalendarService->getAuthUrl());
        }

        return $this->render('booking/index.html.twig', [
            'bookings' => $bookingRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="booking_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $booking = new Booking();
        $today = new \DateTime('today');
        $booking->setStart($today);
        $booking->setEnd($today);

        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $event = $this->googleCalendarService->addEvent($booking);

            if ($event->getRecurrence()) {
                $events = $this->googleCalendarService->getService()->events->instances(GoogleCalendarService::CALENDAR_ID, $event->getId());
                $this->bookingService->saveRecurrenceBookings($booking, $events);
            } else {
                $this->bookingService->saveBooking($booking, $event);
            }

            return $this->redirectToRoute('booking_index');
        }

        return $this->render('booking/new.html.twig', [
            'booking' => $booking,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="booking_show", methods={"GET"})
     */
    public function show(Booking $booking): Response
    {

        return $this->render('booking/show.html.twig', [
            'booking' => $booking,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="booking_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Booking $booking): Response
    {
        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->googleCalendarService->editEvent($booking);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('booking_calendar');
        }

        return $this->render('booking/edit.html.twig', [
            'booking' => $booking,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="booking_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Booking $booking): Response
    {
        if ($this->isCsrfTokenValid('delete' . $booking->getId(), $request->request->get('_token'))) {
            $this->googleCalendarService->deleteEvent($booking->getGoogleId());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($booking);
            $entityManager->flush();
        }

        return $this->redirectToRoute('booking_calendar');
    }
}
