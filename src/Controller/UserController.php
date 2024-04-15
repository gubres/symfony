<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Repository\FamososRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class UserController extends AbstractController
{
    private $entityManager;
    private $userRepository;
    private $famososRepository;

    public function __construct(EntityManagerInterface $entityManager, UserRepository $userRepository, FamososRepository $famososRepository)
    {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->famososRepository = $famososRepository;
    }

    #[Route('/user', name: 'datos_user', methods: ['GET'])]
    public function verUser(EntityManagerInterface $entityManager): Response
    {
        $repository = $entityManager->getRepository(User::class);
        $users = $repository->findAll();

        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/user/data', name: 'user_data', methods: ['GET'])]
    public function userData(): JsonResponse
    {
        $users = $this->userRepository->findNotDeleted();
        $data = array_map(function ($user) {
            return [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'roles' => implode(', ', $user->getRoles()),
                'editar' => '<button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#editModal" data-id="' . $user->getId() . '" data-email="' . $user->getEmail() . '" data-roles="' . implode(', ', $user->getRoles()) . '">Editar</button>',
                'borrar' => '<button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="' . $user->getId() . '" data-email="' . $user->getEmail() . '">Eliminar</button>'
            ];
        }, $users);

        return $this->json(['data' => $data]);
    }


    #[Route('/user/update', name: 'update_user', methods: ['POST'])]
    public function updateUser(Request $request, EntityManagerInterface $entityManager): Response
    {
        $userId = $request->request->get('userId');
        $newRoles = $request->request->all('roles') ?? [];

        $user = $entityManager->getRepository(User::class)->find($userId);
        if ($user) {
            $user->setRoles($newRoles);
            $entityManager->flush();

            $this->addFlash('success', 'Roles actualizados correctamente.');
        } else {
            $this->addFlash('error', 'Usuario no encontrado.');
        }

        return $this->redirectToRoute('datos_user');
    }

    #[Route('/user/delete', name: 'delete_user', methods: ['POST'])]
    public function deleteUser(Request $request): Response
    {
        $userId = $request->request->get('userId');
        $user = $this->userRepository->find($userId);

        if (!$user) {
            $this->addFlash('error', 'Usuario no encontrado.');
            return $this->redirectToRoute('datos_user');
        }

        // Marca el usuario y todos los famosos relacionados como eliminados
        $user->setEliminado(true);
        $famosos = $this->famososRepository->findByUser($user);
        foreach ($famosos as $famoso) {
            $famoso->setEliminado(true);
        }

        $this->entityManager->flush();
        $this->addFlash('success', 'Usuario y sus famosos han sido eliminados.');
        return $this->redirectToRoute('datos_user');
    }
}
