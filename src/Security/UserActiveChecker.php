<?php

namespace App\Security;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserActiveChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user)
    {
        // Vérifier que c'est bien un objet User
        if (!$user instanceof \App\Entity\User) {
            return;
        }

        // Bloquer si l'utilisateur est inactif
        if (!$user->isActive()) {
            throw new CustomUserMessageAccountStatusException(
                'Votre compte a été révoqué et vous ne pouvez pas vous connecter.'
            );
        }
    }

    public function checkPostAuth(UserInterface $user)
    {
    }
}
