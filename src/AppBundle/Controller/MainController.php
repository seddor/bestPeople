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
        return $this->render('main.html.twig', array('users' => $this->getDoctrine()->getRepository('AppBundle:User')->findAll()));
    }
}
