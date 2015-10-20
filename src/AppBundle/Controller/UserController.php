<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Image;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class UserController extends Controller
{
    /**
     * @Route("/id{id}", name="userPage")
     */
    public function indexAction($id)
    {
        return $this->render('user/user_page.html.twig', array(
            'user' => $this->getDoctrine()->getRepository('AppBundle:User')->find($id)));
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

    }
}
