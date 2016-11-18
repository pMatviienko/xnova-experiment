<?php
namespace AuthBundle\Manager;

use FOS\UserBundle\Event\UserEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\HttpFoundation\Request;
use FOS\UserBundle\Doctrine\UserManager as FosUserManager;

class UserManager extends FosUserManager
{
    /**
     * @param array $conditions
     *
     * @return array
     */
    public function findUsersBy(array $conditions)
    {
        return $this->repository->findBy($conditions);
    }
}
