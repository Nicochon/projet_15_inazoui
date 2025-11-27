<?php

namespace App\Tests\Functional;

use App\Entity\User;
use App\Entity\Album;
use App\Entity\Media;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class FrontInactiveUserMediaTest extends WebTestCase
{
    private ?EntityManagerInterface $em = null;
    private ?KernelBrowser $client = null;

    protected function setUp(): void
    {
        // Créer le client avant d'utiliser le container
        $this->client = static::createClient();
        $this->em = static::getContainer()->get(EntityManagerInterface::class);
    }

    public function testInactiveUserMediaIsHiddenOnFront(): void
    {
        // ----- 1) Créer un utilisateur INACTIF -----
        $user = new User();
        $user->setName('User Inactif');
        $user->setEmail('inactive_' . uniqid() . '@test.com'); // unique
        $user->setPassword('password'); // adapter si encodeur
        $user->setActive(false);

        $this->em->persist($user);

        // ----- 2) Créer un album -----
        $album = new Album();
        $album->setName('Album Test ' . uniqid());
        $this->em->persist($album);

        // ----- 3) Ajouter un média à cet utilisateur -----
        $media = new Media();
        $media->setTitle('Image Inactive');
        $media->setPath('test_inactive.jpg');
        $media->setUser($user);
        $media->setAlbum($album);

        $this->em->persist($media);

        $this->em->flush();

        // ----- 4) Appeler la page du FRONT -----
        $crawler = $this->client->request('GET', '/portfolio/' . $album->getId());

        $this->assertResponseIsSuccessful();

        // ----- 5) Vérifier que l'image N'APPARAÎT PAS -----
        $selector = sprintf('img[src*="%s"]', $media->getPath());
        $this->assertCount(
            0,
            $crawler->filter($selector),
            "L'image d'un utilisateur inactif NE DOIT PAS apparaître dans la galerie front."
        );

        // ----- 6) Nettoyer la base -----
        $this->em->remove($media);
        $this->em->remove($album);
        $this->em->remove($user);
        $this->em->flush();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->em = null;
        $this->client = null;
    }
}
