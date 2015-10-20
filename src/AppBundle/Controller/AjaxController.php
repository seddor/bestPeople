<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\History;
use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class AjaxController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @Route("karma", name="updateKarma")
     */
    public function updateKarmaAction(){
        $request = $this->container->get('request');
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($request->query->get('id'));
        $history = new History();

        if($request->query->get('action') === 'up') {
            $user->setKarma($user->getKarma()+1);
            $history->setAction(true);
        }
        else {
            $user->setKarma($user->getKarma()-1);
            $history->setAction(false);
        }

        $history->setUser($user);
        $history->setAuthor($this->getUser());
        $this->getUser()->addHistoryByUser($history);
        $user->addHistory($history);

        $em = $this->getDoctrine()->getManager();

        $em->persist($history);
        $em->persist($user);
        $em->flush();

        $response = array('code' => 100, 'success' => true, 'karma' => $user->getKarma());

        return new Response(json_encode($response));
    }

    /**
     * @Route("top", name="updateTop")
     */
    public function updateTopAction(){


    }


}
