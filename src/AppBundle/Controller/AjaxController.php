<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
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
    public function updateKarmaAction(Request $request){
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($request->get('id'));

        $this->karmaAction($user, $request->get('action'));

        $response = array('karma' => $user->getKarma());

        return new Response(json_encode($response));
    }

    /**
     * @Route("top", name="updateTop")
     */
    public function updateTopAction(Request $request){

        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($request->get('id'));

        $this->karmaAction($user, $request->get('action'));


        $template = $this->forward('AppBundle:Main:top')->getContent();
        $response = new Response(json_encode($template), 200);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    private function karmaAction($user, $action) {

        $history = new History();

        if($action === 'up') {
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


    }


}
