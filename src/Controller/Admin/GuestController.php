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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class GuestController extends AbstractController
{
    /**
     * @Route("admin/guests", name="admin_guest_index")
     */
    public function guests(ManagerRegistry $doctrine): Response
    {
        $guests = $doctrine->getRepository(User::class)->findAll();

        return $this->render('admin/guests/index.html.twig', [
            'guests' => $guests
        ]);
    }

    /**
     * @Route("/admin/guest/add", name="admin_guest_add")
     */
    public function add(Request $request, ManagerRegistry $doctrine, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

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

            return $this->redirectToRoute('admin_guest_index');
        }

        return $this->render('/admin/guests/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/guest/delete/{id}', name: 'admin_guest_delete', methods: ['GET'])]
    public function delete(User $user, ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $entityManager = $doctrine->getManager();
        $entityManager->remove($user);
        $entityManager->flush();

        $this->addFlash('success', 'L’invité et son contenu associé ont été supprimés avec succès.');

        return $this->redirectToRoute('admin_guest_index');
    }

    #[Route('/admin/guest/revoke/{id}', name: 'admin_guest_revoke')]
    public function revoke(User $user, ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $user->setActive(false);
        $doctrine->getManager()->flush();

        $this->addFlash('warning', "L’accès de {$user->getName()} a été révoqué.");
        return $this->redirectToRoute('admin_guest_index');
    }

    #[Route('/admin/guest/activate/{id}', name: 'admin_guest_activate')]
    public function activate(User $user, ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $user->setActive(true);
        $doctrine->getManager()->flush();

        $this->addFlash('success', "L’accès de {$user->getName()} a été réactivé.");
        return $this->redirectToRoute('admin_guest_index');
    }
}