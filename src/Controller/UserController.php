<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\BatchRepository;
use App\Repository\UserRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var BatchRepository
     */
    private $batchRepository;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param UserRepository  $userRepository
     * @param BatchRepository $batchRepository
     */
    public function __construct(UserRepository $userRepository, BatchRepository $batchRepository)
    {
        $this->userRepository = $userRepository;
        $this->batchRepository = $batchRepository;
    }

    /**
     * @Route("/", name="user_index", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $this->userRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="user_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_delete", methods={"DELETE"})
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index');
    }

    /**
     * @Route("/getUsersByBatch/{batchId}", name="get_users_by_batch", methods={"GET"}, options={"expose"=true})
     */
    public function getUsersByBatch(Request $request, int $batchId)
    {
        if ($request->isXmlHttpRequest()) {
            $batch = $this->batchRepository->find($batchId);
            $users = $batch->getUsers();

            if ($users) {
                $encoders = [
                    new JsonEncoder(),
                ];
                $normalizers = [
                    new ObjectNormalizer(),
                ];
                $serializer = new Serializer($normalizers, $encoders);
                $data = $serializer->serialize($users, 'json',
                    [
                        'circular_reference_handler' => function ($object) {
                            return $object->getId();
                        },
                    ]);

                return new JsonResponse($data, 200, [], true);
            }
        }

        return new JsonResponse("This function is only available in AJAX");
    }
}
