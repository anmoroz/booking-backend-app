<?php
/*
 * This file is part of the Reservation application project.
 *
 * https://github.com/anmoroz
 */

declare(strict_types=1);


namespace App\Service;

use App\Core\Exception\UserIsNotAuthenticateException;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserService
{
    private ?User $currentUser = null;

    /**
     * @param User|null $currentUser
     */
    public function __construct(private TokenStorageInterface $tokenStorage)
    {
    }

    /**
     * @return User
     */
    public function getCurrentUser(): User
    {
        if (is_null($this->currentUser)) {
            if ($this->tokenStorage->getToken()) {
                /** @var \App\Entity\User $user */
                $user = $this->tokenStorage->getToken()->getUser();
                if (is_object($user) && $user instanceof User) {
                    $this->currentUser = $user;
                }
            }
        }

        if (!$this->currentUser) {
            throw new UserIsNotAuthenticateException();
        }

        return $this->currentUser;
    }
}