<?php

namespace AuthBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

/**
 * Class UserAdmin.
 */
class UserAdmin extends AbstractAdmin
{
    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->clearExcept(array('create','list', 'delete', 'edit'));
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('username')
            ->add('usernameCanonical')
            ->add('email')
            ->add('enabled')
            ->add('locked')
            ->add('expired')
            ->add('lastLogin')
//            ->add('expiresAt')
            ->add(
                'roles',
                'array',
                ['template' => 'AuthBundle:admin:roles_list_field.html.twig']
            )->add(
                'groups',
                'array',
                ['template' => 'AuthBundle:admin:roles_list_field.html.twig']
            )
            ->add('_action', 'actions', array(
                'actions' => array(
                    'view' => array(),
                    'edit' => array(),
                    'delete' => array(),
                ),
            ))
        ;
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('username')
            ->add('usernameCanonical')
            ->add('email')
            ->add('enabled')
            ->add('locked')
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('General')
            ->add('username', 'text')
            ->add('usernameCanonical', 'text')
            ->add('email', 'email')
            ->add('emailCanonical', 'email')
            ->add('password')
            ->add('enabled')
            ->add('locked')
            ->add('expired')
            ->add('groups', 'sonata_type_model', array('multiple' => true,))
            ->end()
            ->end();
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('username')
            ->add('usernameCanonical')
        ;
    }
}
