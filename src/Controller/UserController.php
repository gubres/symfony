<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;

class UserController extends AbstractController
{
    #[Route('/user', name: 'datos_user', methods: ['GET'])]
    public function verUser(EntityManagerInterface $entityManager): Response
    {
        $repository = $entityManager->getRepository(User::class);
        $users = $repository->findAll();

        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
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
}
