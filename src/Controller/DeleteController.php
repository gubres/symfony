<?php

// src/Controller/UsuarioDeleteController.php

namespace App\Controller;

use App\Entity\Famosos;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DeleteController extends AbstractController
{
    #[Route('/{id}', name: 'famosos_delete', methods: ['GET'])]
    public function delete(Request $request, Famosos $famoso, EntityManagerInterface $entityManager): Response
    {
    // Establece el campo 'eliminado' a true
    $famoso->setEliminado(true);
    $entityManager->flush();

    // Redirige al usuario a la pagina principal
    return $this->redirectToRoute('app_main');
    }
}
