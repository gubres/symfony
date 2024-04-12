<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;
use App\Form\RegistrationFormType;

class RegistrationController extends AbstractController
{
    #[Route('/registro', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $user->getEmail()]);
            if ($existingUser) {

                // mensaje de error al formulario en caso de que ya exista el correo en la BDD
               $this->addFlash('error', 'El correo electrónico ya está en uso. Elige otro');
                return $this->redirectToRoute('app_register');
            }

            // copia la contraseña a un textoplano y la hashed
            $user->setPassword($passwordHasher->hashPassword($user, $form->get('plainPassword')->getData()));
            
            $entityManager->persist($user);
            $entityManager->flush();
            //aqui limpio la contraseña que estaba en el textoplano para no persistir 
            //a la base de datos
            $user->eraseCredentials();
   
            return $this->redirectToRoute('app_login');
        }
        return $this->render('registration/registro.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
