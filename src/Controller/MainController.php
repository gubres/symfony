<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Famosos;
use App\Entity\User;
use App\Form\FamososType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\FamososRepository;
use Symfony\Component\Validator\Validator\ValidatorInterface;




class MainController extends AbstractController
{
    private Security $security;
    private FamososRepository $famososRepository;

    public function __construct(Security $security, FamososRepository $famososRepository)
    {
        $this->security = $security;
        $this->famososRepository = $famososRepository;
    }

    #[Route('/home', name: 'app_main')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // Aquí se usa directamente el repositorio inyectado.
        $famosos = $entityManager->getRepository(Famosos::class)->findNotDeleted();
        $famoso = new Famosos();
        $form = $this->createForm(FamososType::class, $famoso);

        return $this->render('main/index.html.twig', [
            'famosos' => $famosos,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/main/formulario', name: 'form_datos', methods: ['POST'])]
    public function rellenarFormulario(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator): Response
    {
        $user = $this->security->getUser();
        if (!$user) {
            // Redirige o maneja casos donde el usuario no está autenticado
            throw $this->createAccessDeniedException('No estás autorizado para realizar esta acción.');
        }

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
                $famoso->setCreatedBy($user);
                $entityManager->persist($famoso);
                $entityManager->flush();

                return new JsonResponse(['success' => true]);
            }
        }

        return new JsonResponse(['success' => false], Response::HTTP_BAD_REQUEST);
    }



    #[Route('/famosos/datostabla', name: 'datos_tabla', methods: ['GET'])]
    public function verTabla(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            // redirigir al usuario a la página de inicio de sesión
            return $this->json(['error' => 'Usuario no autenticado'], Response::HTTP_FORBIDDEN);
        }

        $draw = $request->query->get('draw', 1);
        $start = $request->query->get('start', 0);
        $length = $request->query->get('length', 10);
        $search = $request->query->all()['search']['value'] ?? '';
        $order = $request->query->all()['order'][0] ?? ['column' => 0, 'dir' => 'asc'];

        // método que incluye filtros y paginación
        $famosos = $this->famososRepository->findByUserWithPagination($user, $start, $length, $search, $order);
        $recordsTotal = $this->famososRepository->countNotDeletedByUser($user);
        $recordsFiltered = $recordsTotal; // Si aplicas otros filtros actualiza este valor.

        $data = array_map(function ($famoso) {
            return [
                'id' => $famoso->getId(),
                'nombre' => $famoso->getNombre(),
                'apellido' => $famoso->getApellido(),
                'profesion' => $famoso->getProfesion(),
                'editar' => '<button class="btn btn-primary editarBtn" data-bs-toggle="modal" data-bs-target="#editarModal" data-id="' . $famoso->getId() . '">Editar</button>',
                'borrar' => '<a href="#" data-id="' . $famoso->getId() . '" data-nombre="' . $famoso->getNombre() . '" class="btn btn-danger btnDelete">Borrar</a>'
            ];
        }, $famosos);

        return new JsonResponse([
            'draw' => intval($draw),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ]);
    }
}
