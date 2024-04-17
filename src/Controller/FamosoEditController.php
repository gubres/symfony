<?php

namespace App\Controller;

use DateTime;
use DateTimeZone;
use App\Entity\Famosos;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FamosoEditController extends AbstractController
{
    #[Route('/editar/{id}', name: 'famosos_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Famosos $famoso, EntityManagerInterface $entityManager): Response
    {

        if ($request->isMethod('POST')) {
            $nombre = $request->request->get('nombre');
            $apellido = $request->request->get('apellido');
            $profesion = $request->request->get('profesion');

            $famoso->setNombre($nombre);
            $famoso->setApellido($apellido);
            $famoso->setProfesion($profesion);
            $famoso->setModificado(new DateTime('now', new DateTimeZone('Europe/Madrid')));
            $entityManager->persist($famoso);
            $entityManager->flush();

            return new JsonResponse(['success' => true]);
        }

        return new JsonResponse(['success' => false, 'message' => 'Método no permitido'], Response::HTTP_METHOD_NOT_ALLOWED);
    }


    #[Route('/info/{id}', name: 'famosos_info', methods: ['GET'])]
    public function getFamosoInfo(Famosos $famoso): JsonResponse
    {
        // devuelve los datos del famoso en formato JSON
        return new JsonResponse([
            'id' => $famoso->getId(),
            'nombre' => $famoso->getNombre(),
            'apellido' => $famoso->getApellido(),
            'profesion' => $famoso->getProfesion(),
        ]);
    }
}
