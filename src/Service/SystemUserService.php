<?php

namespace App\Service;

use App\Entity\SystemUser;
use Symfony\Component\Security\Core\Security;

class SystemUserService
{
    public function __construct(private Security $security)
    {
    }

    /**
     * @return SystemUser
     */
    public function getCurrentUser(): SystemUser
    {
        if($this->security->isGranted('ROLE_ADMIN'))
        {
            $user = $this->security->getUser();
            if($user instanceof SystemUser)
            {
                return $user;
            }
        }
    }
}