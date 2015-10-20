<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Image;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class UserController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
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
//            //pas
            $plainPassword = $request->get('_password');
            $encoder = $this->container->get('security.password_encoder');
            $encoded = $encoder->encodePassword($user, $plainPassword);
            $user->setPassword($encoded);

//            $user->setPassword($request->get('_password'));

            $user->setGender($request->get('_gender'));
            $user->setKarma(0);

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
}
