<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Famosos;
use App\Form\FamososType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

class MainController extends AbstractController
{
    #[Route('/main/index', name: 'app_main')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Crear una nueva entidad Famosos para el formulario
        $famoso = new Famosos();
        $form = $this->createForm(FamososType::class, $famoso);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Aquí, el objeto $famoso ya tiene los datos del formulario,
            // incluyendo nombre, apellido, y profesión.
            // Los campos automáticos como creado, modificado y eliminado
            // deben estar configurados para manejarse automáticamente en la entidad o aquí antes de persistir.
            $entityManager->persist($famoso);
            $entityManager->flush();
  
            // Redirecciona de vuelta a la página principal donde se listan los famosos
            return $this->redirectToRoute('app_main');
        }

        // Obtener todos los registros de famosos no eliminados para mostrar en la página principal
        // El Famososrepository tiene el método `findNotDeleted` que filtra por el campo 'eliminado'
            return $this->render('main/index.html.twig', [
                'famosos' => $entityManager->getRepository(Famosos::class)->findNotDeleted(),
                'form' => $form->createView(),
        ]);
    }
}
