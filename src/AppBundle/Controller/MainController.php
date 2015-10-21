<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class MainController extends Controller
{
     /**
     * @Route("/", name="main")
     */
    public function indexAction()
    {
        return $this->render('main.html.twig');
    }

    public function topAction()
    {
        $users = $this->getDoctrine()->getRepository('AppBundle:User')->findBy(
            array(),
            array('karma' => 'DESC'),
            15
        );

        return $this->render(':user:top.html.twig', array('users' => $users));
    }
}
