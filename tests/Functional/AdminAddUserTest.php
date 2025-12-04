<?php

namespace App\Tests\Functional;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;


class AdminAddUserTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $em;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->em = $this->client->getContainer()->get('doctrine')->getManager();
    }

    public function testAdminCanAddUser(): void
    {
        // 1) Créer un admin
        $admin = new User();
        $admin->setName('Admin Test');
        $admin->setEmail('admin_adduser@test.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setActive(true);
        $admin->setPassword(password_hash('password', PASSWORD_BCRYPT));
        $this->em->persist($admin);
        $this->em->flush();

        // 2) Se connecter
        $this->client->loginUser($admin);

        // 3) Envoyer le formulaire d'ajout
        $crawler = $this->client->request('GET', '/admin/guest/add');

        $form = $crawler->selectButton('Ajouter')->form([
            'user[name]' => 'Nouvel Utilisateur',
            'user[email]' => 'nouvel.user@test.com',
            'user[password]' => 'motdepasseTest',
            'user[admin]' => false,
        ]);

        $this->client->submit($form);

        // 4) Vérifier l'utilisateur créé
        $userRepo = $this->em->getRepository(User::class);
        $user = $userRepo->findOneBy(['email' => 'nouvel.user@test.com']);

        $this->assertNotNull($user, 'L’utilisateur a bien été créé');
        $this->assertEquals('Nouvel Utilisateur', $user->getName());
        $this->assertEquals(['ROLE_USER'], $user->getRoles());

        // 5) Nettoyage : supprimer seulement le nouvel utilisateur
        $userRepo = $this->em->getRepository(User::class);

        if ($user = $userRepo->findOneBy(['email' => 'nouvel.user@test.com'])) {
            $this->em->remove($user);
        }

        if ($admin = $userRepo->findOneBy(['email' => 'admin_adduser@test.com'])) {
            $this->em->remove($admin);
        }

        $this->em->flush();
    }

    protected function tearDown(): void
    {
        $this->em->close();
        parent::tearDown(); // éviter les problèmes de mémoire
    }
}
