<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Famosos;
use App\Form\FamososType;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;


class MainController extends AbstractController
{
    #[Route('/home', name: 'app_main')]
public function index(EntityManagerInterface $entityManager): Response
{
    // Obtener todos los registros de famosos no eliminados para mostrar en la página principal.
    // Asumiendo que el método `findNotDeleted()` está implementado en el repositorio de Famosos para filtrar por el campo 'eliminado'.
    $famosos = $entityManager->getRepository(Famosos::class)->findNotDeleted();

    // Crea una nueva instancia de la entidad Famosos para el formulario.
    $famoso = new Famosos();
    $form = $this->createForm(FamososType::class, $famoso);

    // Renderiza la página con la tabla de famosos y el formulario vacío listo para ser utilizado en la ventana modal.
    return $this->render('main/index.html.twig', [
        'famosos' => $famosos,
        'form' => $form->createView(),
    ]);
}

    #[Route('/main/formulario', name: 'form_datos', methods: ['POST'])]
    public function rellenarFormulario(Request $request, EntityManagerInterface $entityManager): JsonResponse {
        $famoso = new Famosos();
        $form = $this->createForm(FamososType::class, $famoso);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($famoso);
            $entityManager->flush();
            
            return new JsonResponse(['success' => true]);
        }
    
        return new JsonResponse(['success' => false], Response::HTTP_BAD_REQUEST);
    }
    
    #[Route('/main/datostabla', name:'datos_tabla', methods: ['GET'])]
    public function verTabla(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $draw = $request->query->get('draw', 1);
        $start = $request->query->get('start', 0);
        $length = $request->query->get('length', 10);
        $search = $request->query->all()['search']['value'] ?? '';
        $order = $request->query->all()['order'][0] ?? ['column' => 0, 'dir' => 'asc'];
    
        $repository = $entityManager->getRepository(Famosos::class);
    
        $famosos = $repository->findNotDeletedWithCriteria($start, $length, $search, $order);
        $recordsFiltered = $repository->countFilteredNotDeleted($search);
        $recordsTotal = $repository->countNotDeleted();
    
        $data = [];
        foreach ($famosos as $famoso) {
            $data[] = [
                'id' => $famoso->getId(),
                'nombre' => $famoso->getNombre(),
                'apellido' => $famoso->getApellido(),
                'profesion' => $famoso->getProfesion(),
                'editar' => '<a href="' . $this->generateUrl('famosos_edit', ['id' => $famoso->getId()]) . '" class="btn btn-primary">Editar</a>',
                'borrar' => '<a href="' . $this->generateUrl('famosos_delete', ['id' => $famoso->getId()]) . '" class="btn btn-danger" onclick="return confirmDelete(\'' . $famoso->getNombre() . '\')">Borrar</a>'
            ];
        }
    
        return new JsonResponse([
            'draw' => intval($draw),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ]);
    }
    
}
