<?php

namespace App\Controller;

use App\Entity\Request as DbRequest;


use App\Repository\StatusRepository;
use App\Repository\RequestRepository;


use App\Form\RequestFormType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\HttpFoundation\JsonResponse;


use Symfony\Component\Security\Core\Security;


use Symfony\Component\Routing\Annotation\Route;


class HomeController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
       $this->security = $security;
    }

    #[Route('/', name: 'app_home')]
    public function index(Request $request, EntityManagerInterface $entityManager, StatusRepository $statusRepository, RequestRepository $requestRepository): Response
    {

        $request_entity = new DbRequest();
        $form = $this->createForm(RequestFormType::class, $request_entity);
        $form->handleRequest($request);
        $requests = $requestRepository->findAll();
        $user = $this->security->getUser();

        if($user->getRoles('ROLE_ADMIN')){
            $status = $statusRepository->findAll();
        }

        if ($form->isSubmitted() && $form->isValid()) {

            $request_entity->setUser($user);
            $status = $statusRepository->findOneByName("waiting");
            $request_entity->setStatus($status);

            $entityManager->persist($request_entity);
            $entityManager->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('home/index.html.twig', [
            'requestForm' => $form->createView(),
            'requests' => $requests,
            'status' => $status,
        ]);
    }

    #[Route('/status', name: 'change_status', methods: ['POST'] )]
    public function changeStatusAction(Request $request, EntityManagerInterface $entityManager, RequestRepository $requestRepository, StatusRepository $statusRepository){


        $data = $request->request->all();

        $dbrequest = $requestRepository->findOneById($data["request"]);
        $status = $statusRepository->findOneById($data["status"]);


        $dbrequest->setStatus($status);

        $entityManager->persist($dbrequest);
        $entityManager->flush();

        return new JsonResponse(array(
            'status' => "ok",
            'message' => "ok"
        ), 200);
    }
}
