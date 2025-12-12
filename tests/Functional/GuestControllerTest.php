<?php

namespace App\Tests\Functional;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GuestControllerTest extends WebTestCase
{
    public function testDeleteGuest(): void
{
    $client = static::createClient();
    $em = $client->getContainer()->get('doctrine')->getManager();

    // Création d'un utilisateur admin
    $admin = new User();
    $admin->setName('Admin Test');
    $admin->setEmail('admin'.uniqid().'@test.com');
    $admin->setRoles(['ROLE_ADMIN']);
    $admin->setActive(true);
    $admin->setPassword(password_hash('password', PASSWORD_BCRYPT));
    $em->persist($admin);
    $em->flush();

    $client->loginUser($admin);

    // Création d'un invité à supprimer
    $guest = new User();
    $guest->setName('Guest Test');
    $guest->setEmail('guest'.uniqid().'@test.com');
    $guest->setRoles(['ROLE_USER']);
    $guest->setActive(true);
    $guest->setPassword(password_hash('guestpass', PASSWORD_BCRYPT));
    $em->persist($guest);
    $em->flush();

    $guestId = $guest->getId();

    // Appel de la route de suppression
    $client->request('GET', '/admin/guest/delete/'.$guestId);

    // Vérification de la redirection correcte
    $this->assertResponseRedirects('/admin/guests');
    $this->assertResponseStatusCodeSame(302);

    // Vérification de la suppression
    $deletedGuest = $em->getRepository(User::class)->find($guestId);
    $this->assertNull($deletedGuest, 'L’invité devrait avoir été supprimé');

    // Nettoyage de l'admin
    $adminToRemove = $em->getRepository(User::class)->find($admin->getId());
    if ($adminToRemove) {
        $em->remove($adminToRemove);
        $em->flush();
    }
}
}
