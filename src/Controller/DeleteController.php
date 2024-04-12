<?php

// src/Controller/UsuarioDeleteController.php

namespace App\Controller;

use App\Entity\Famosos;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use DateTime;
use DateTimeZone;

class DeleteController extends AbstractController
{
    #[Route('main/index/{id}', name: 'famosos_delete', methods: ['GET'])]
    public function delete(Request $request, Famosos $famoso, EntityManagerInterface $entityManager): Response
    {
    // establece el campo 'eliminado' a true, eliminado logico
    $famoso->setEliminado(true);
    //cambia el campo modificado con la fecha actual
    $famoso->setModificado(new DateTime('now', new DateTimeZone('Europe/Madrid')));
    $entityManager->flush();

    return new JsonResponse(['success' => true]);
    }
}
