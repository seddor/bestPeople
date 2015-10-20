<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Comment;
use AppBundle\Entity\History;
use AppBundle\Entity\Image;
use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class UserController extends Controller
{
    /**
     * @Route("/id{id}", name="userPage")
     */
    public function indexAction($id)
    {
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
        $complete = null;
//        if($this->getUser()->getId() == $id)
//            $complete = true;
        foreach($this->getUser()->getHistoryByUser()->getValues() as $history) {
            if($history->getAuthor()->getId() == $this->getUser()->getId()) {
                $complete = $history;
            }
        }


        return $this->render('user/user_page.html.twig', array(
            'user' => $user,
            'complete' => $complete)
        );
    }

    /**
     * @Route("/registration", name="registration")
     */
    public function registrationAction(Request $request)
    {
        if($this->isGranted('ROLE_USER'))
            return $this->redirectToRoute('main');

        $image = new Image();
        $form = $this->createFormBuilder($image)
            ->add('file','file',array(
                'required' => false
            ))
            ->getForm();

        $form->handleRequest($request);

        if($form->isValid()) {
            $user = new User();
            $user->setUsername(mb_strtolower($request->get('_username')));
            //pas
            $plainPassword = $request->get('_password');
            $encoder = $this->container->get('security.password_encoder');
            $encoded = $encoder->encodePassword($user, $plainPassword);
            $user->setPassword($encoded);

            $user->setGender($request->get('_gender'));

            $em = $this->getDoctrine()->getManager();

            if (!$form->get('file')->isEmpty()) {
                $user->setAvatar($image);
                $em->persist($user);
                $em->flush();
                $image->upload($user->getId());
//                $image->setPath($user->getId().'/'.$form->get('file')->getData()->getClientOriginalName());
                $em->persist($image);
                $em->flush();
                return $this->redirectToRoute('main');
            }

            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('main');
        }

        return $this->render(':user:registration.html.twig', array('imageForm' => $form->createView()));
    }

    /**
     * @Route("id{id}/writeComment", name="commentFormAction")
     */
    public function commentAction($id, Request $request) {
        $comment = new Comment();

        $comment->setText($request->get('_comment'));
        $comment->setAuthor($this->getUser());
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
        $comment->setUser($user);

        $em = $this->getDoctrine()->getManager();
        $em->persist($this->getUser());
        $em->persist($user);
        $em->persist($comment);
        $em->flush();

        return $this->redirectToRoute('userPage',array('id' => $id));
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
}
