<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Famosos;
use App\Form\FamososType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class MainController extends AbstractController
{
    #[Route('/home', name: 'app_main')]
public function index(EntityManagerInterface $entityManager): Response
{
    // obtiene todos los registros de famosos no eliminados para mostrar en la página principal.
    // el método `findNotDeleted()` está implementado en el repositorio de Famosos para filtrar por el campo 'eliminado'.
    $famosos = $entityManager->getRepository(Famosos::class)->findNotDeleted();

    $famoso = new Famosos();
    $form = $this->createForm(FamososType::class, $famoso);

    return $this->render('main/index.html.twig', [
        'famosos' => $famosos,
        'form' => $form->createView(),
    ]);
}

#[Route('/main/formulario', name: 'form_datos', methods: ['POST'])]
public function rellenarFormulario(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator): JsonResponse {
    $famoso = new Famosos();
    $form = $this->createForm(FamososType::class, $famoso);
    $form->handleRequest($request);

    if ($form->isSubmitted()) {
        $errors = $validator->validate($famoso);
    
        if (count($errors) > 0) {
            $errorsString = (string) $errors;
            return new JsonResponse(['success' => false, 'message' => $errorsString]);
        }
    
        if ($form->isValid()) {
            $entityManager->persist($famoso);
            $entityManager->flush();
        
            return new JsonResponse(['success' => true]);
        }
    }

    return new JsonResponse(['success' => false], Response::HTTP_BAD_REQUEST);
}

    
    #[Route('/famosos/datostabla', name:'datos_tabla', methods: ['GET'])]
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
                'editar' => '<button class="btn btn-primary editarBtn" data-bs-toggle="modal" data-bs-target="#editarModal" data-id="' . $famoso->getId() . '">Editar</button>',
                'borrar' => '<a href="#" data-id="' . $famoso->getId() . '" data-nombre="' . $famoso->getNombre() . '" class="btn btn-danger btnDelete">Borrar</a>'
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
