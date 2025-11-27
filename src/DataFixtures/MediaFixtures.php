<?php

namespace App\DataFixtures;

use App\Entity\Media;
use App\Entity\User;
use App\Entity\Album;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class MediaFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $users = $manager->getRepository(User::class)->findAll();
        $albums = $manager->getRepository(Album::class)->findAll();

        foreach ($users as $user) {
            for ($i = 1; $i <= 2; $i++) {
                $media = new Media();
                $media->setUser($user);
                $media->setTitle("Media $i de " . $user->getName());
                $media->setPath("uploads/media/user{$user->getId()}_media$i.jpg");

                $album = $albums[array_rand($albums)];
                $media->setAlbum($album);

                $manager->persist($media);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            AlbumFixtures::class,
        ];
    }
}
