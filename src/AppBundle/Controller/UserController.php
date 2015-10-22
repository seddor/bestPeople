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
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class UserController extends Controller
{
    /**
     * @Route("/id{id}", name="userPage")
     */
    public function indexAction($id)
    {
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
        $complete = null;

        if ($this->isGranted('ROLE_USER')) {
            foreach($this->getUser()->getHistoryByUser()->getValues() as $history) {
                if($history->getUser()->getId() == $user->getId()) {
                    $complete = $history;
                }
            }
        }

        return $this->render('user/user_page.html.twig', array(
            'user' => $user,
            'complete' => $complete)
        );
    }

    public function historyAction($id) {
        return $this->render('user/history.html.twig', array('histories' => $this->getDoctrine()->getRepository('AppBundle:User')->find($id)->getHistories()));
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

        if ($form->getErrors(true)->count() != 0 and !$form->get('file')->isEmpty()) {
            $error = 'Изображение должно быть не больше 5МБ, в формате .png, .gif, .jpg';
            return $this->render('user/registration.html.twig', array('error' => $error,'imageForm' => $form->createView()));
        }

        if($form->isValid()) {
            $user = new User();
            $user->setUsername(mb_strtolower($request->get('_username')));
            //pas
//            $plainPassword = $request->get('_password');
//            $encoder = $this->container->get('security.password_encoder');
//            $encoded = $encoder->encodePassword($user, $plainPassword);
//            $user->setPassword($encoded);
            if (preg_match('/[0-9]/',$request->get('_password')) == 0 or preg_match('/[A-Za-z]/',$request->get('_password')) == 0) {
                $error = 'Пароль должен содержать латинские бкувы и хотя-бы одну цифру';
                return $this->render('user/registration.html.twig', array('error' => $error,'imageForm' => $form->createView()));
            }


            $user->setPassword($request->get('_password'));

            $user->setGender($request->get('_gender'));

            $em = $this->getDoctrine()->getManager();

            if (!$form->get('file')->isEmpty()) {
                $user->setAvatar($image);
                $em->persist($user);
                $em->flush();
                $image->upload($user->getId());
                try {
                    $em->persist($user);
                    $em->flush();
                } catch(\Exception $e) {
                    $error = 'Логин уже занят';
                    return $this->render('user/registration.html.twig', array('error' => $error,'imageForm' => $form->createView()));
                }

                $token = new UsernamePasswordToken($user, $user->getPassword(), 'database_users',$user->getRoles() );
                $this->get('security.token_storage')->setToken($token);

                $this->get('image.handling')->open($image->getAbsolutePath())
                    ->resize(50, 50)->save($image->getAbsolutePath());

                return $this->redirectToRoute('main');
            }

            try {
                $em->persist($user);
                $em->flush();
            } catch(\Exception $e) {
                $error = 'Логин уже занят';
                return $this->render('user/registration.html.twig', array('error' => $error,'imageForm' => $form->createView()));
            }


            $token = new UsernamePasswordToken($user, $user->getPassword(), 'database_users',$user->getRoles() );
            $this->get('security.token_storage')->setToken($token);

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
     * @Route("id{id}/edit", name="editUser")
     */
    public function editAction($id, Request $request) {
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);

        $image = new Image();
        $form = $this->createFormBuilder($image)
            ->add('file','file',array(
                'required' => false,
                'label' => 'Аватар:'
            ))
            ->getForm();

        $form->handleRequest($request);

        if ($form->getErrors(true)->count() != 0 and !$form->get('file')->isEmpty()) {
            $error = 'Изображение должно быть не больше 5МБ, в формате .png, .gif, .jpg';
            return $this->render(':user:edit_user.html.twig',array('user' => $user,'imageForm' => $form->createView(), 'error' => $error));
        }

        if($form->isValid()) {
            $user->setGender($request->get('_gender'));

            $em = $this->getDoctrine()->getManager();

            if (!$form->get('file')->isEmpty()) {
                $user->getAvatar()->removeUpload();
                $user->setAvatar($image);
                $em->persist($user);
                $em->flush();
                $image->upload($user->getId());
                $em->persist($image);
                $em->flush();

                $this->get('image.handling')->open($image->getAbsolutePath())
                    ->resize(50, 50)->save($image->getAbsolutePath());

                return $this->redirectToRoute('userPage',array('id' => $user->getId()));
            }

            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('userPage',array('id' => $user->getId()));
        }


        return $this->render(':user:edit_user.html.twig',array('user' => $user,'imageForm' => $form->createView()));
    }

    /**
     * @Route("id{id}/cancelVote", name="cancelVote")
     */
    public function cancelVoteAction($id) {
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);

        $em = $this->getDoctrine()->getManager();
        foreach($user->getHistories()->getValues() as $history) {
            if($history->getAuthor()->getId() == $this->getUser()->getId()) {
                if($history->getAction()) {
                    $user->setKarma($user->getKarma()-1);
                }else
                    $user->setKarma($user->getKarma()+1);
                $user->removeHistory($history);
                $this->getUser()->removeHistoryByUser($history);
                $em->remove($history);
            }
        }

        $em->persist($user);
        $em->persist($this->getUser());
        $em->flush();

        return $this->redirectToRoute('userPage',array('id' => $id));
    }



}
