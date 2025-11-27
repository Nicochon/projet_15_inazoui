<?php

namespace App\Controller;

use App\Entity\Album;
use App\Entity\Media;
use App\Entity\User;
use App\Service\MediaFilterService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function home(UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
//        $email = 'ina@zaoui.com';
//
//        $user = $userRepository->findOneBy(['email' => $email]);
//
//        $hashedPassword = $passwordHasher->hashPassword($user, 'motdepasseTest');
//        $user->setPassword($hashedPassword);
//
//// 3️⃣ Ajouter un rôle
//        $roles = $user->getRoles();
//        if (!in_array('ROLE_ADMIN', $roles, true)) {
//            $roles[] = 'ROLE_ADMIN';
//        }
//        $user->setRoles($roles);
//
//// 4️⃣ Sauvegarder
//        $entityManager->persist($user);
//        $entityManager->flush();
//
//
//        dump($user);
//        die;

        return $this->render('front/home.html.twig');
    }

    /**
     * @Route("/guests", name="guests")
     */
    public function guests(ManagerRegistry $doctrine): Response
    {
        $guests = $doctrine->getRepository(User::class)->findBy(['admin' => false]);
        return $this->render('front/guests.html.twig', [
            'guests' => $guests
        ]);
    }

    /**
     * @Route("/guest/{id}", name="guest")
     */
    public function guest(int $id, ManagerRegistry $doctrine): Response
    {
        $guest = $doctrine->getRepository(User::class)->find($id);

        if (!$guest) {
            throw $this->createNotFoundException('Utilisateur introuvable');
        }

        return $this->render('front/guest.html.twig', [
            'guest' => $guest
        ]);
    }

    /**
     * @Route("/portfolio/{id?}", name="portfolio")
     */
    #[Route('/portfolio/{id?}', name: 'portfolio')]
    public function portfolio( ManagerRegistry $doctrine, MediaFilterService $mediaFilterService, ?int $id = null): Response
    {
        // 1. Injection des Repositories via ManagerRegistry
        $albumRepository = $doctrine->getRepository(Album::class);
        $userRepository  = $doctrine->getRepository(User::class);
        $mediaRepository = $doctrine->getRepository(Media::class);

        // 2. Récupération des données
        $albums = $albumRepository->findAll();
        $album  = $id ? $albumRepository->find($id) : null;
        $user   = $userRepository->findOneBy(['admin' => true]);

        // 3. Récupération des médias
        if ($album) {
            $medias = $mediaRepository->findBy(['album' => $album]);
        } elseif ($user) {
            $medias = $mediaRepository->findBy(['user' => $user]);
        } else {
            $medias = [];
        }

        // Filtrer les médias : ne garder que ceux dont l'utilisateur est actif
        $medias = $mediaFilterService->filterActiveUsers($medias);

        // 5. Rendu du template
        return $this->render('front/portfolio.html.twig', [
            'albums' => $albums,
            'album'  => $album,
            'medias' => $medias,
        ]);
    }

    /**
     * @Route("/about", name="about")
     */
    public function about(): Response
    {
        return $this->render('front/about.html.twig');
    }
}