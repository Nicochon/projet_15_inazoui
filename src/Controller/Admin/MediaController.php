<?php

namespace App\Controller\Admin;

use App\Entity\Media;
use App\Form\MediaType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MediaController extends AbstractController
{
    /**
     * @Route("/admin/media", name="admin_media_index")
     */
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $page = $request->query->getInt('page', 1);

        $criteria = [];

        if (!$this->isGranted('ROLE_ADMIN')) {
            $criteria['user'] = $this->getUser();
        }

        $medias = $em->getRepository(Media::class)->findBy(
            $criteria,
            ['id' => 'ASC'],
            25,
            25 * ($page - 1)
        );

        $total = (int) $em->createQueryBuilder()
            ->select('COUNT(m.id)')
            ->from(Media::class, 'm')
            ->getQuery()
            ->getSingleScalarResult();

        return $this->render('admin/media/index.html.twig', [
            'medias' => $medias,
            'total' => $total,
            'page' => $page
        ]);
    }

    /**
     * @Route("/admin/media/add", name="admin_media_add")
     */
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $media = new Media();
        $form = $this->createForm(MediaType::class, $media, ['is_admin' => $this->isGranted('ROLE_ADMIN')]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $media->getFile();

            if (!$file) {
                $this->addFlash('danger', 'Aucun fichier téléchargé.');
                return $this->redirectToRoute('admin_media_add');
            }

            $mimeType = $file->getMimeType();
            if (!str_starts_with($mimeType, 'image/')) {
                $this->addFlash('danger', 'Le fichier doit être une image.');
                return $this->redirectToRoute('admin_media_add');
            }

            $maxSize = 2 * 1024 * 1024; // 2 Mo en octets
            if ($file->getSize() > $maxSize) {
                $this->addFlash('danger', 'Le fichier ne doit pas dépasser 2 Mo.');
                return $this->redirectToRoute('admin_media_add');
            }

            if (!$this->isGranted('ROLE_ADMIN')) {
                $user = $this->getUser();
                if (!$user instanceof \App\Entity\User) {
                    throw new \LogicException('Utilisateur invalide.');
                }
                $media->setUser($user);
            }

            $fileName = md5(uniqid()) . '.' . $media->getFile()->guessExtension();
            $media->setPath('uploads/' . $fileName);

            $media->getFile()->move(
                $this->getParameter('kernel.project_dir') . '/public/uploads',
                $fileName
            );


            $entityManager->persist($media);
            $entityManager->flush();

//            $media->setPath('uploads/' . md5(uniqid()) . '.' . $media->getFile()->guessExtension());
//            $media->getFile()->move('uploads/', $media->getPath());
//            $this->getDoctrine()->getManager()->persist($media);
//            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_media_index');
        }

        return $this->render('admin/media/add.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/admin/media/delete/{id}", name="admin_media_delete")
     */
    public function delete(int $id, EntityManagerInterface $em): Response
    {
        $media = $em->getRepository(Media::class)->find($id);

        if (!$media) {
            throw $this->createNotFoundException('Media not found.');
        }

        $em->remove($media);
        $em->flush();

        if (file_exists($media->getPath())) {
            unlink($media->getPath());
        }

        return $this->redirectToRoute('admin_media_index');
    }
}