<?php

namespace PM\FosapiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('PMFosapiBundle:Default:index.html.twig');
    }
}
