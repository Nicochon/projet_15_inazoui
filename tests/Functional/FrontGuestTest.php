<?php

namespace App\Tests\Functional;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FrontGuestTest extends WebTestCase
{
    public function testGuestPage(): void
    {
        $client = static::createClient();
        $entityManager = $client->getContainer()->get('doctrine')->getManager();

        // Créer un utilisateur temporaire
        $guest = new User();
        $guest->setName('Test Guest');
        $guest->setEmail('guest@test.com');
        $guest->setDescription('Quo ductus ex struuntur incertum Tyrum multi apud dum rector ideoque nominis dum ideoque indumentum rector nominis struuntur struuntur cuius.');
        $guest->setRoles(['ROLE_USER']);
        $guest->setActive(true);
        $guest->setPassword(password_hash('password', PASSWORD_BCRYPT));
        $entityManager->persist($guest);
        $entityManager->flush();

        // Accéder à la page guest/{id}
        $crawler = $client->request('GET', '/guest/'.$guest->getId());

        // Vérifier que la réponse est 200 OK
        $this->assertResponseIsSuccessful();

        // Vérifier que le nom du guest apparaît dans le contenu de la page
        $this->assertStringContainsString('Test Guest', $client->getResponse()->getContent());

        // Nettoyage
        $guestToRemove = $entityManager->getRepository(User::class)->find($guest->getId());
        if ($guestToRemove) {
            $entityManager->remove($guestToRemove);
            $entityManager->flush();
        }
    }

    public function testGuestPageNotFound(): void
    {
        $client = static::createClient();

        // Utiliser un ID qui n'existe pas
        $nonExistentId = 999999;

        // Accéder à la page guest/{id}
        $client->request('GET', '/guest/'.$nonExistentId);

        // Vérifier que la page renvoie 404
        $this->assertResponseStatusCodeSame(404);
    }
}
