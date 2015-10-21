<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class SecurityController extends Controller
{
//    /**
//     * @Route("/login", name="login_form")
//     */
//    public function loginAction(Request $request)
//    {
//        if($this->isGranted('ROLE_USER'))
//            return $this->redirectToRoute('main');
//
//        $authenticationUtils = $this->get('security.authentication_utils');
//
//        $error = $authenticationUtils->getLastAuthenticationError();
//
//        $lastUserName = $authenticationUtils->getLastUsername();
//
//        $captcaForm = $this->createFormBuilder()
//            ->add('captcha','captcha')->getForm();
//
//        return $this->render(':user:login.html.twig',
//            array(
//                'last_username' => $lastUserName,
//                'error' => $error,
//            )
//        );
//    }
//
//    /**
//     * @Route("/login_check", name="login_check")
//     */
//    public function loginCheckAction()
//    {
//
//    }

    /**
     * @Route("/login", name="login_form")
     */
    public function lolAction(Request $request)
    {
        if($this->isGranted('ROLE_USER'))
            return $this->redirectToRoute('main');

        $captchaForm = $this->createFormBuilder()
            ->add('captcha','captcha')->getForm();

        $captchaForm->handleRequest($request);

        if($captchaForm->isValid()) {

            $user = $this->getDoctrine()->getRepository('AppBundle:User')->findOneBy(array('username' => $request->get('_username')));

            if($user == null) {
                $error = 'Пользователя не существует';
                return $this->render(':user:login.html.twig', array('error' => $error,'captcha' => $captchaForm->createView()));
            }

            if ($user->getPassword() == $request->get('password')) {
                $token = new UsernamePasswordToken($user, $user->getPassword(), 'database_users',$user->getRoles() );
                $this->get('security.token_storage')->setToken($token);
                return $this->redirectToRoute('main');
            } else {
                $error = 'Неверный пароль';
                return $this->render(':user:login.html.twig', array('error' => $error,'captcha' => $captchaForm->createView()));
            }




        }

        return $this->render(':user:login.html.twig',
            array('captcha' => $captchaForm->createView()
            )
        );
    }

}