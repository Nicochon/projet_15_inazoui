<?php

namespace App\Tests\Functional;

use App\Entity\Album;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AlbumControllerTest extends WebTestCase
{
    public function testAddAlbumPage(): void
    {
        $client = static::createClient();
        $em = $client->getContainer()->get('doctrine')->getManager();

        // Créer un utilisateur admin avec email unique
        $admin = new User();
        $admin->setName('Admin Test');
        $admin->setEmail('admin'.uniqid().'@test.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setActive(true);
        $admin->setPassword(password_hash('password', PASSWORD_BCRYPT));
        $em->persist($admin);
        $em->flush();

        $client->loginUser($admin);

        // Accéder à la page add
        $crawler = $client->request('GET', '/admin/album/add');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form[name=album]');

        // Soumettre le formulaire avec un nom d'album
        $form = $crawler->selectButton('Ajouter')->form([
            'album[name]' => 'Nouvel Album',
        ]);
        $client->submit($form);

        // Vérifier la redirection
        $this->assertResponseRedirects('/admin/album');
        $client->followRedirect();
        $this->assertResponseIsSuccessful();

        // Vérifier que l'album a été créé
        $albumRepo = $em->getRepository(Album::class);
        $albumCreated = $albumRepo->findOneBy(['name' => 'Nouvel Album']);
        $this->assertNotNull($albumCreated);

        // Nettoyage : recharger les entités avant remove
        $albumToRemove = $albumRepo->find($albumCreated->getId());
        if ($albumToRemove) {
            $em->remove($albumToRemove);
        }

        $adminToRemove = $em->getRepository(User::class)->find($admin->getId());
        if ($adminToRemove) {
            $em->remove($adminToRemove);
        }

        $em->flush();
    }

    public function testUpdateAlbum(): void
    {
        $client = static::createClient();
        $em = $client->getContainer()->get('doctrine')->getManager();

        // Créer un utilisateur admin avec email unique
        $admin = new User();
        $admin->setName('Admin Test');
        $admin->setEmail('admin'.uniqid().'@test.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setActive(true);
        $admin->setPassword(password_hash('password', PASSWORD_BCRYPT));
        $em->persist($admin);
        $em->flush();

        $client->loginUser($admin);

        // Créer un album de test
        $album = new Album();
        $album->setName('Old Name');
        $em->persist($album);
        $em->flush();

        // Accéder à la page de mise à jour
        $crawler = $client->request('GET', '/admin/album/update/'.$album->getId());
        $this->assertResponseIsSuccessful();

        // Remplir le formulaire
        $form = $crawler->selectButton('Modifier')->form();
        $form['album[name]'] = 'New Name';
        $client->submit($form);

        $this->assertResponseRedirects('/admin/album');
        $client->followRedirect();
        $this->assertResponseIsSuccessful();

        // Vérifier que le nom a été mis à jour
        $updatedAlbum = $em->getRepository(Album::class)->find($album->getId());
        $this->assertEquals('New Name', $updatedAlbum->getName());

        // Nettoyage : recharger les entités avant remove
        $albumToRemove = $em->getRepository(Album::class)->find($album->getId());
        if ($albumToRemove) {
            $em->remove($albumToRemove);
        }

        $adminToRemove = $em->getRepository(User::class)->find($admin->getId());
        if ($adminToRemove) {
            $em->remove($adminToRemove);
        }

        $em->flush();
    }
}
