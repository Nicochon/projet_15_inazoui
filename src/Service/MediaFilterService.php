<?php

namespace App\Service;

use App\Entity\Media;

class MediaFilterService
{
    /**
     * Filtre un tableau de médias pour ne garder que ceux dont l'utilisateur est actif.
     *
     * @param Media[] $medias
     *
     * @return Media[]
     */
    public function filterActiveUsers(array $medias): array
    {
        return array_filter($medias, function (Media $media) {
            $user = $media->getUser();

            return $user && $user->isActive();
        });
    }
}
