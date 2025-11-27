<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Création de 5 utilisateurs
        for ($i = 1; $i <= 5; ++$i) {
            $user = new User();
            $user->setName("User $i");
            $user->setEmail("user$i@example.com");
            $user->setDescription("Description de l'utilisateur $i");
            $user->setActive(true);
            $user->setAdmin(false);
            $user->setRoles(['ROLE_USER']);

            $hashedPassword = $this->passwordHasher->hashPassword($user, 'password');
            $user->setPassword($hashedPassword);

            $manager->persist($user);
        }

        $manager->flush();
    }
}
