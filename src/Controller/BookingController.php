<?php

namespace App\Controller;

use App\Client\GoogleClient;
use App\Entity\Booking;
use App\Form\BookingType;
use App\Repository\BookingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/booking")
 */
class BookingController extends AbstractController
{
    /**
     * @var GoogleClient
     */
    private $handler;
    private $client;
    /**
     * @var Session
     */
    private $session;

    public function __construct(GoogleClient $client)
    {

        $this->client = $client;
    }

    /**
     * @Route("/calendar", name="booking_calendar", methods={"GET"})
     */
    public function calendar(): Response
    {
        return $this->render('booking/calendar.html.twig');
    }

    /**
     * @Route("/code", name="booking_code", methods={"GET"})
     */
    public function code(Request $request): Response
    {
        if ($request->query->get('code')) {
            $this->client->setAcccessToken($request->query->get('code'));
        }
        return $this->redirectToRoute('booking_index');
    }

    /**
     * @Route("/", name="booking_index", methods={"GET"})
     */
    public function index(Request $request, BookingRepository $bookingRepository): Response
    {
        $session = new Session();
//        dd($session->clear('access_token'));
//        dd($session->get('access_token'));
        if (!$this->client->getAccessToken()) {
            return $this->redirect($this->client->getAuthUrl());
        }
        $this->client->getClient()->setAccessToken($this->client->getAccessToken());
        $service = new \Google_Service_Calendar($this->client->getClient());
        $calendarId = 'primary';
        $optParams = [
            'maxResults' => 10,
            'orderBy' => 'startTime',
            'singleEvents' => true,
            'timeMin' => date('c'),
        ];
        $results = $service->events->listEvents($calendarId, $optParams);
        $events = $results->getItems();

        foreach ($events as $event) {
            var_dump($event->getId());
        }

//        $event = new \Google_Service_Calendar_Event([
//            'summary' => 'Moje wydarzenie',
//            'location' => '800 Howard St., San Francisco, CA 94103',
//            'sendUpdates' => 'all',
//            'description' => 'A chance to hear more about Google\'s developer products.',
//            'start' => [
//                'dateTime' => '2019-10-04T09:00:00-07:00',
//                'timeZone' => 'America/Los_Angeles',
//            ],
//            'end' => [
//                'dateTime' => '2019-10-04T17:00:00-07:00',
//                'timeZone' => 'America/Los_Angeles',
//            ],
//        ]);
//        $event = $service->events->insert($calendarId, $event, ['sendNotifications' => true]);
//        var_dump($event);
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
        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($booking);
            $entityManager->flush();

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
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('booking_index');
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
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($booking);
            $entityManager->flush();
        }

        return $this->redirectToRoute('booking_index');
    }


    public function authorize(Request $request)
    {
        $this->client = $this->getClient($request);
        if ($session->get('access_token')) {
            $accessToken = $session->get('access_token');
        } else {
            $authUrl = $this->client->createAuthUrl();
            return $this->redirect($authUrl);
        }
        $this->client->setAccessToken($accessToken);
    }
}
