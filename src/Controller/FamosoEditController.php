<?php
// src/Controller/UsuarioEditController.php

namespace App\Controller;

use App\Entity\Famosos;
use App\Form\FamososType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FamosoEditController extends AbstractController
{
    #[Route('/Famosos/edit/{id}', name: 'famosos_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Famosos $famoso, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FamososType::class, $famoso);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_main');
        }

        return $this->render('Famosos/edit.html.twig', [
            'famoso' => $famoso,
            'form' => $form->createView(),
        ]);
    }
}
