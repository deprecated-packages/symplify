<?php

declare(strict_types=1);

namespace Zenify\DoctrineBehaviors\Blameable;

use Nette\Security\User;

final class UserCallable
{

    /**
     * @var User
     */
    private $user;


    public function __construct(User $user)
    {
        $this->user = $user;
    }


    /**
     * @return mixed
     */
    public function __invoke()
    {
        if ($this->user->isLoggedIn()) {
            return $this->user->getId();
        }
        
    }
}
