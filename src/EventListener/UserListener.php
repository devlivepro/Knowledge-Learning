<?php

namespace App\EventListener;

use App\Entity\User;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Bundle\SecurityBundle\Security;

class UserListener
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function preUpdate(User $user, PreUpdateEventArgs $event): void
    {
        $user->setUpdatedAt(new \DateTimeImmutable());

        $editor = $this->security->getUser();
        if ($editor instanceof User) {
            $user->setUpdatedBy($editor);
        }
    }

    public function prePersist(User $user, LifecycleEventArgs $event): void
    {
        // To be sure even at creation if ever missing
        if (!$user->getCreatedAt()) {
            $user->setCreatedAt(new \DateTimeImmutable());
        }
        $user->setUpdatedAt(new \DateTimeImmutable());

        $actor = $this->security->getUser();
        if ($actor instanceof User) {
            if (!$user->getCreatedBy()) {
                $user->setCreatedBy($actor);
            }
            $user->setUpdatedBy($actor);
        }
    }
}