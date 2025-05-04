<?php

namespace App\Security;

use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use App\Entity\User;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }

        if (!$user->isActivated()) {
            throw new CustomUserMessageAccountStatusException('Votre compte n\'est pas encore activé. Veuillez vérifier vos emails.');
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        // Nothing for the moment here
    }
}
