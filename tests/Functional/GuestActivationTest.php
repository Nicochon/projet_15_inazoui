<?php

namespace App\Tests\Functional;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GuestActivationTest extends WebTestCase
{
    public function testRevokeUser(): void
    {
        $client = static::createClient();
        $entityManager = $client->getContainer()->get('doctrine')->getManager();

        // Créer un utilisateur admin
        $admin = new User();
        $admin->setName('Admin Test');
        $admin->setEmail('admin@test.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setActive(true);
        $admin->setPassword(password_hash('password', PASSWORD_BCRYPT));
        $entityManager->persist($admin);

        // Créer un utilisateur normal
        $user = new User();
        $user->setName('Utilisateur Test');
        $user->setEmail('user@test.com');
        $user->setRoles(['ROLE_USER']);
        $user->setActive(true);
        $user->setPassword(password_hash('password', PASSWORD_BCRYPT));
        $entityManager->persist($user);

        $entityManager->flush();

        // Se connecter en tant qu’admin
        $client->loginUser($admin);

        // Révoquer l'utilisateur
        $client->request('GET', '/admin/guest/revoke/' . $user->getId());
        $entityManager->refresh($user);
        $this->assertFalse($user->isActive(), 'L’utilisateur doit être inactif après révoquer');

        // Nettoyage
        $entityManager->remove($user);
        $entityManager->remove($admin);
        $entityManager->flush();
    }

    public function testActivateUser(): void
    {
        $client = static::createClient();
        $entityManager = $client->getContainer()->get('doctrine')->getManager();

        // Créer un utilisateur admin
        $admin = new User();
        $admin->setName('Admin Test');
        $admin->setEmail('admin@test.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setActive(true);
        $admin->setPassword(password_hash('password', PASSWORD_BCRYPT));
        $entityManager->persist($admin);

        // Créer un utilisateur normal inactif
        $user = new User();
        $user->setName('Utilisateur Test');
        $user->setEmail('user@test.com');
        $user->setRoles(['ROLE_USER']);
        $user->setActive(false);
        $user->setPassword(password_hash('password', PASSWORD_BCRYPT));
        $entityManager->persist($user);

        $entityManager->flush();

        // Se connecter en tant qu’admin
        $client->loginUser($admin);

        // Activer l'utilisateur
        $client->request('GET', '/admin/guest/activate/' . $user->getId());
        $entityManager->refresh($user);
        $this->assertTrue($user->isActive(), 'L’utilisateur doit être actif après activation');

        // Nettoyage
        $entityManager->remove($user);
        $entityManager->remove($admin);
        $entityManager->flush();
    }
}
