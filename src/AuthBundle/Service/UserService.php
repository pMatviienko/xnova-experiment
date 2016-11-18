<?php
namespace AuthBundle\Service;

use AuthBundle\Entity\User;
use FOS\UserBundle\Event\UserEvent;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Util\UserManipulator;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use AuthBundle\Manager\UserManager;

class UserService
{
    /**
     * @var UserManager
     */
    protected $userManager;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var UserManipulator
     */
    private $manipulator;

    /**
     * UserService constructor.
     *
     * @param UserManager              $userManager
     * @param EventDispatcherInterface $dispatcher
     * @param UserManipulator          $manipulator
     */
    public function __construct(UserManager $userManager, EventDispatcherInterface $dispatcher, UserManipulator $manipulator)
    {
        $this->userManager = $userManager;
        $this->dispatcher = $dispatcher;
        $this->manipulator = $manipulator;
    }

    /**
     * Creates a user and returns it.
     *
     * @param string $username
     * @param string $password
     * @param string $email
     * @param bool   $active
     * @param User   $masterAccount
     *
     * @return \FOS\UserBundle\Model\UserInterface
     */
    public function createUser($username, $password, $email, $active, User $masterAccount)
    {
        $user = $this->manipulator->create($username, $password, $email, $active, false);
        $user->setMaster($masterAccount);
        $user->setRoles($masterAccount->getRoles());
        $this->userManager->updateUser($user);
        return $user;
    }

    /**
     * @param User    $user
     * @param string  $username
     * @param string  $email
     * @param boolean $isEnabled
     *
     * @return User
     */
    public function updateUser(User $user, $username, $email, $isEnabled = null)
    {
        $user->setUsername($username);
        $user->setEmail($email);

        $wasEnabled = $user->isEnabled();

        if(null != $isEnabled && $wasEnabled != $isEnabled) {
            $user->setEnabled($isEnabled);
            $this->userManager->updateUser($user);

            if($isEnabled){
                $this->dispatcher->dispatch(
                    FOSUserEvents::USER_ACTIVATED,
                    new UserEvent($user)
                );
            } else {
                $this->dispatcher->dispatch(
                    FOSUserEvents::USER_DEACTIVATED,
                    new UserEvent($user)
                );
            }
        } else {
            $this->userManager->updateUser($user);
        }

        return $user;
    }

    /**
     * @param User   $user
     * @param string $password
     *
     * @return User
     */
    public function setUserPassword(User $user, $password)
    {
        $user->setPlainPassword($password);
        $this->userManager->updateUser($user);

        $this->dispatcher->dispatch(
            FOSUserEvents::USER_PASSWORD_CHANGED,
            new UserEvent($user)
        );

        return $user;
    }
}
