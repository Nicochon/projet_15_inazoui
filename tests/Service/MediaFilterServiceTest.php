<?php

namespace App\Tests\Service;

use App\Entity\Media;
use App\Entity\User;
use App\Service\MediaFilterService;
use PHPUnit\Framework\TestCase;

class MediaFilterServiceTest extends TestCase
{
    public function testFilterActiveUsers(): void
    {
        $service = new MediaFilterService();

        $activeUser = new User();
        $activeUser->setActive(true);

        $inactiveUser = new User();
        $inactiveUser->setActive(false);

        $media1 = new Media();
        $media1->setUser($activeUser);

        $media2 = new Media();
        $media2->setUser($inactiveUser);

        $media3 = new Media();
        $media3->setUser(null);

        $medias = [$media1, $media2, $media3];

        $result = $service->filterActiveUsers($medias);

        $this->assertCount(1, $result);
        $this->assertSame($media1, array_values($result)[0]);
    }
}
