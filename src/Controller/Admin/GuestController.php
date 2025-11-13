<?php

namespace App\Controller\Admin;

use App\Entity\Album;
use App\Entity\Media;
use App\Entity\User;
use App\Form\AlbumType;
use App\Form\MediaType;
use App\Form\UserType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class GuestController extends AbstractController
{
    /**
     * @Route("admin/guests", name="admin_guest_index")
     */
    public function guests(ManagerRegistry $doctrine)
    {
        $guests = $doctrine->getRepository(User::class)->findBy(['admin' => false]);
        return $this->render('admin/guests/index.html.twig', [
            'guests' => $guests
        ]);
    }

    /**
     * @Route("/admin/guest/add", name="admin_guest_add")
     */
    public function add(Request $request, ManagerRegistry $doctrine, UserPasswordHasherInterface $passwordHasher)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $user->getPassword()
            );
            $user->setPassword($hashedPassword);

            if ($user->isAdmin()) {
                $user->setRoles(['ROLE_ADMIN']);
            } else {
                $user->setRoles(['ROLE_USER']);
            }

            $entityManager = $doctrine->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // Redirection après l'ajout
            return $this->redirectToRoute('admin_guest_index');
        }

        // Affichage du formulaire
        return $this->render('/admin/guests/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}