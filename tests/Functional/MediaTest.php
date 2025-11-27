<?php

namespace App\Tests\Functional;

use App\Entity\Album;
use App\Entity\Media;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MediaTest extends WebTestCase
{
    public function testAddMedia(): void
    {
        $client = static::createClient();
        $entityManager = $client->getContainer()->get('doctrine')->getManager();

        // Créer un utilisateur admin
        $user = new User();
        $user->setName('Admin Test');
        $user->setEmail('admin@test.com');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setActive(true);
        $user->setPassword(password_hash('password', PASSWORD_BCRYPT));

        $entityManager->persist($user);
        $entityManager->flush();

        $album = new Album();
        $album->setName('Album Test');
        $entityManager->persist($album);
        $entityManager->flush();

        // Se connecter avec l'utilisateur admin
        $client->loginUser($user);

        // Aller sur la page d'ajout
        $crawler = $client->request('GET', '/admin/media/add');

        $this->assertResponseIsSuccessful();

        // Créer un fichier image temporaire
        $file = tempnam(sys_get_temp_dir(), 'test');
        imagepng(imagecreatetruecolor(10, 10), $file);
        $uploadedFile = new UploadedFile($file, 'test.png', 'image/png', null, true);

        // Remplir le formulaire
        $form = $crawler->selectButton('Ajouter')->form([
            'media[file]' => $uploadedFile,
            'media[title]' => 'Test image',
            'media[user]' => $user->getId(),
            'media[album]' => $album->getId(),
        ]);

        // Soumettre le formulaire
        $client->submit($form);

        // Vérifier la redirection
        $this->assertResponseRedirects('/admin/media');

        $client->followRedirect();
        $this->assertResponseIsSuccessful();

        // Vérifier que le média a été ajouté en base
        $media = $entityManager->getRepository(Media::class)->findOneBy(['title' => 'Test image']);
        $this->assertNotNull($media);
        $this->assertEquals('Test image', $media->getTitle());

        $user = $entityManager->getRepository(User::class)->find($user->getId());
        if ($user) {
            $entityManager->remove($user);
        }

        $album = $entityManager->getRepository(Album::class)->find($album->getId());
        if ($album) {
            $entityManager->remove($album);
        }

        $media = $entityManager->getRepository(Media::class)->find($media->getId());
        if ($media) {
            $entityManager->remove($media);
        }
        $entityManager->flush();

        // Supprimer le fichier temporaire
        unlink($file);
        $uploadDir = $this->getContainer()->getParameter('kernel.project_dir') . '/public/uploads/';
        if (file_exists($uploadDir . $media->getPath())) {
            unlink($uploadDir . $media->getPath());
        }
    }
}
