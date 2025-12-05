<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testUserCanLogin(): void
    {
        // Aller sur la page de connexion
        $crawler = $this->client->request('GET', '/login');

        // Remplir le formulaire
        $form = $crawler->selectButton('Connexion')->form([
            '_username' => 'user1@example.com',
            '_password' => 'password',
        ]);

        // Soumettre le formulaire
        $this->client->submit($form);

        // Vérifier la redirection vers la homepage
        $this->assertResponseRedirects('/');
        $this->client->followRedirect();

        // Vérifier que la page est chargée
        $this->assertResponseIsSuccessful();
    }

    public function testLoginWithNonExistingUser(): void
    {
        $crawler = $this->client->request('GET', '/login');

        $form = $crawler->selectButton('Connexion')->form([
            '_username' => 'nonexistent@example.com',
            '_password' => 'wrongpassword',
        ]);

        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        // Vérifier qu'on reste sur la page de login
        $this->assertStringContainsString('/login', $this->client->getRequest()->getUri());

        // Vérifier que le message d'erreur apparaît
        $this->assertSelectorTextContains('.alert-danger', 'Invalid credentials.');
    }

    public function testInactiveUserCannotLogin(): void
    {
        $entityManager = $this->client->getContainer()->get('doctrine')->getManager();

        // Créer un utilisateur inactif
        $inactiveUser = new \App\Entity\User();
        $inactiveUser->setName('Inactif Test');
        $inactiveUser->setEmail('inactive@test.com');
        $inactiveUser->setRoles(['ROLE_USER']);
        $inactiveUser->setActive(false);
        $inactiveUser->setPassword(password_hash('password', PASSWORD_BCRYPT));
        $entityManager->persist($inactiveUser);
        $entityManager->flush();

        // Aller sur la page de login
        $crawler = $this->client->request('GET', '/login');

        $form = $crawler->selectButton('Connexion')->form([
            '_username' => 'inactive@test.com',
            '_password' => 'password',
        ]);

        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        // Vérifier qu'on reste sur la page de login
        $this->assertStringContainsString('/login', $this->client->getRequest()->getUri());

        // Vérifier que le message d'erreur spécifique à l'inactivité apparaît
        $this->assertSelectorTextContains('.alert-danger', 'Votre compte a été révoqué et vous ne pouvez pas vous connecter.');

        // Nettoyage
        $inactiveUser = $entityManager->getRepository(\App\Entity\User::class)->find($inactiveUser->getId());
        $entityManager->remove($inactiveUser);
        $entityManager->flush();
    }
}
