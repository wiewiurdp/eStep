<?php

namespace App\Controller;

use App\Entity\Role;
use App\Entity\Attendee;
use App\Form\AttendeeType;
use App\Repository\BatchRepository;
use App\Repository\RoleRepository;
use App\Repository\AttendeeRepository;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_USER")
 *
 * @Route("/attendee")
 */
class AttendeeController extends AbstractController
{
    /**
     * @var AttendeeRepository
     */
    private $attendeeRepository;
    /**
     * @var BatchRepository
     */
    private $batchRepository;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var RoleRepository
     */
    private $roleRepository;

    /**
     * AttendeeController constructor.
     *
     * @param AttendeeRepository $attendeeRepository
     */
    public function __construct(AttendeeRepository $attendeeRepository)
    {
        $this->attendeeRepository = $attendeeRepository;
    }

    /**
     * @Route("/", name="attendee_index", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('attendee/index.html.twig', [
            'attendees' => $this->attendeeRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="attendee_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $attendee = new Attendee();
        $form = $this->createForm(AttendeeType::class, $attendee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($attendee);
            $entityManager->flush();

            return $this->redirectToRoute('attendee_index');
        }

        return $this->render('attendee/new.html.twig', [
            'attendee' => $attendee,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="attendee_show", methods={"GET"})
     */
    public function show(Attendee $attendee): Response
    {
        return $this->render('attendee/show.html.twig', [
            'attendee' => $attendee,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="attendee_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Attendee $attendee): Response
    {
        $form = $this->createForm(AttendeeType::class, $attendee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('attendee_index');
        }

        return $this->render('attendee/edit.html.twig', [
            'attendee' => $attendee,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="attendee_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Attendee $attendee): Response
    {
        if ($this->isCsrfTokenValid('delete' . $attendee->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($attendee);
            $entityManager->flush();
        }

        return $this->redirectToRoute('attendee_index');
    }

    /**
     * @Route("/getAttendeesByBatch/{batchId}", name="get_attendees_by_batch", methods={"GET"}, options={"expose"=true})
     */
    public function getAttendeesByBatch(Request $request, int $batchId): JsonResponse
    {
        if ($request->isXmlHttpRequest()) {
            $attendeesObjects = $this->attendeeRepository->getAttendeesByBatchId($batchId);
            $attendeesAndRole = json_encode($this->getAttendeesAndRoleByBatchId($attendeesObjects, $batchId));
            return new JsonResponse($attendeesAndRole, 200, [], true);
        }

        return new JsonResponse('This function is only available in AJAX');
    }

    /**
     * @param $attendeesObjects
     * @param $batchId
     *
     * @return array
     */
    private function getAttendeesAndRoleByBatchId($attendeesObjects, $batchId): array
    {
        $attendeesAndRole = [];
        /** @var Attendee $attendeeObject */
        foreach ($attendeesObjects as $key => $attendeeObject) {
            $attendeesAndRole[$key]['id'] = $attendeeObject->getId();
            $attendeesAndRole[$key]['name'] = sprintf('%s %s', $attendeeObject->getName(), $attendeeObject->getSurname());
            /** @var Role $role */
            $attendeesAndRole[$key]['role'] = null;
            foreach ($attendeeObject->getRoles() as $role) {
                if ($role->getBatch()->getId() === $batchId) {
                    $attendeesAndRole[$key]['role'] = $role->getName();
                }
            }
        }
        $keys = array_column($attendeesAndRole, 'role');
        array_multisort($keys, SORT_DESC, $attendeesAndRole);

        return $attendeesAndRole;
    }

}
