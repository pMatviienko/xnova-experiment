<?php
namespace AuthBundle\Controller;

use AuthBundle\Entity\User;
use AuthBundle\Manager\UserManager;
use AuthBundle\Service\UserService;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\UserBundle\Model\UserInterface;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class SubAccountsController extends FOSRestController
{
    /**
     * Gets a sub accounts list
     *
     *  **Response example**
     *
     *      [
     *          {
     *              id: "2",
     *              username: "test",
     *              username_canonical: "test",
     *              email: "test@pp.int",
     *              email_canonical: "test@pp.int",
     *              enabled: false,
     *              locked: false,
     *              expired: false,
     *              master: {
     *                  id: "1",
     *                  username: "admin",
     *                  username_canonical: "admin",
     *                  email: "admin@app.int",
     *                  email_canonical: "admin@app.int",
     *                  enabled: true,
     *                  last_login: "2016-10-17T13:48:31+0000",
     *                  locked: false,
     *                  expired: false
     *              }
     *          }
     *      ]
     *
     * @View(serializerGroups={
     *       "user_list"
     * })
     *
     * @ApiDoc(
     *     description="Gets a sub accounts list",
     *     section="Accounts and Auth",
     *     statusCodes={
     *      200="Call was successful",
     *      403="The Request was not authorized",
     *      500="A server side error occurred"
     *     }
     * )
     *
     * @return UserInterface[]
     */
    public function listAction()
    {
        /**
         * @var UserManager $userManager
         */
        $userManager = $this->get('fos_user.user_manager');
        return $userManager->findUsersBy(['master' => $this->getMasterUserAccount()->getId()]);
    }

    /**
     * Gets a sub account details
     *
     *  **Response example**
     *
     *      [
     *          {
     *              id: "2",
     *              username: "test",
     *              username_canonical: "test",
     *              email: "test@pp.int",
     *              email_canonical: "test@pp.int",
     *              enabled: false,
     *              locked: false,
     *              expired: false,
     *              master: {
     *                  id: "1",
     *                  username: "admin",
     *                  username_canonical: "admin",
     *                  email: "admin@app.int",
     *                  email_canonical: "admin@app.int",
     *                  enabled: true,
     *                  last_login: "2016-10-17T13:48:31+0000",
     *                  locked: false,
     *                  expired: false
     *              }
     *          }
     *      ]
     *
     * @View(serializerGroups={
     *       "user_details"
     * })
     *
     * @ApiDoc(
     *     description="Gets a sub account details",
     *     section="Accounts and Auth",
     *     statusCodes={
     *      200="Call was successful",
     *      403="The Request was not authorized",
     *      500="A server side error occurred"
     *     }
     * )
     *
     * @return User
     *
     * @throws AccessDeniedException
     */
    public function detailsAction($id)
    {
        /**
         * @var UserManager $userManager
         */
        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->findUserBy(['id' => $id]);
        if ($user->getMaster()->getId() != $this->getMasterUserAccount()->getId()) {
            throw $this->createAccessDeniedException('You can not edit this subaccount');
        }
        
        return $user;
    }

    /**
     * Updating user account.
     *
     *  **Response example**
     *
     *      [
     *          {
     *              id: "2",
     *              username: "test",
     *              username_canonical: "test",
     *              email: "test@pp.int",
     *              email_canonical: "test@pp.int",
     *              enabled: false,
     *              locked: false,
     *              expired: false,
     *              master: {
     *                  id: "1",
     *                  username: "admin",
     *                  username_canonical: "admin",
     *                  email: "admin@app.int",
     *                  email_canonical: "admin@app.int",
     *                  enabled: true,
     *                  last_login: "2016-10-17T13:48:31+0000",
     *                  locked: false,
     *                  expired: false
     *              }
     *          }
     *      ]
     *
     * @View(serializerGroups={
     *       "user_details"
     * })
     *
     * @ApiDoc(
     *     description="Updating user account.",
     *     section="Accounts and Auth",
     *     statusCodes={
     *      200="Call was successful",
     *      403="The Request was not authorized",
     *      500="A server side error occurred"
     *     }
     * )
     *
     * @RequestParam(name="username", description="Username")
     * @RequestParam(name="email", description="User email")
     * @RequestParam(name="password", allowBlank=true, nullable=true, description="User password")
     * @RequestParam(name="enabled", nullable=true, description="User Is Enabled")
     *
     * @param integer               $id
     * @param ParamFetcherInterface $paramFetcher
     *
     * @return User
     *
     * @throws AccessDeniedException
     */
    public function updateAction($id, ParamFetcherInterface $paramFetcher)
    {
        /**
         * @var UserManager $userManager
         */
        $userManager = $this->get('fos_user.user_manager');

        /**
         * @var User $user
         */
        $user = $userManager->findUserBy(['id' => $id]);

        if ($user->getMaster()->getId() != $this->getMasterUserAccount()->getId()) {
            throw $this->createAccessDeniedException('You can not edit this subaccount');
        }

        /**
         * @var UserService $service
         */
        $service = $this->get('auth.service.user_service');
        $service->updateUser(
            $user,
            $paramFetcher->get('username'),
            $paramFetcher->get('email'),
            $paramFetcher->get('enabled')
        );
        $newPassword = $paramFetcher->get('password');

        if(!empty($newPassword)) {
            $service->setUserPassword($user, $newPassword);
        }

        return $user;
    }

    /**
     * Creates user account.
     *
     *  **Response example**
     *
     *      [
     *          {
     *              id: "2",
     *              username: "test",
     *              username_canonical: "test",
     *              email: "test@pp.int",
     *              email_canonical: "test@pp.int",
     *              enabled: false,
     *              locked: false,
     *              expired: false,
     *              master: {
     *                  id: "1",
     *                  username: "admin",
     *                  username_canonical: "admin",
     *                  email: "admin@app.int",
     *                  email_canonical: "admin@app.int",
     *                  enabled: true,
     *                  last_login: "2016-10-17T13:48:31+0000",
     *                  locked: false,
     *                  expired: false
     *              }
     *          }
     *      ]
     *
     * @View(serializerGroups={
     *       "user_details"
     * })
     *
     * @ApiDoc(
     *     description="Creates a user subaccount",
     *     section="Accounts and Auth",
     *     statusCodes={
     *      200="Call was successful",
     *      403="The Request was not authorized",
     *      500="A server side error occurred"
     *     }
     * )
     *
     * @RequestParam(name="username", description="Username")
     * @RequestParam(name="email", description="User email")
     * @RequestParam(name="password", description="User password")
     *
     * @param ParamFetcherInterface $paramFetcher
     *
     * @return UserInterface
     */
    public function createAction(ParamFetcherInterface $paramFetcher)
    {
        /**
         * @var UserService $service
         */
        $service = $this->get('auth.service.user_service');

        $user = $service->createUser(
            $paramFetcher->get('username'),
            $paramFetcher->get('password'),
            $paramFetcher->get('email'),
            true,
            $this->getMasterUserAccount()
        );

        return $user;
    }

    /**
     * Deleting user
     *
     * @View()
     *
     * @ApiDoc(
     *     description="Deleting user",
     *     section="Accounts and Auth",
     *     statusCodes={
     *      200="Call was successful",
     *      403="The Request was not authorized",
     *      500="A server side error occurred"
     *     }
     * )
     *
     * @param $id
     *
     * @return array
     *
     * @throws AccessDeniedException
     */
    public function deleteAction($id)
    {
        /**
         * @var UserManager $userManager
         */
        $userManager = $this->get('fos_user.user_manager');
        /**
         * @var User $user
         */
        $user = $userManager->findUserBy(['id' => $id]);
        if ($user->getMaster()->getId() != $this->getMasterUserAccount()->getId()) {
            throw $this->createAccessDeniedException('You can not edit this subaccount');
        }
        $userManager->deleteUser($user);
        return ['response'=> true];
    }

    /**
     * @return User
     */
    private function getMasterUserAccount()
    {
        return $this->getUser()->getMasterUserAccount();
    }
}
