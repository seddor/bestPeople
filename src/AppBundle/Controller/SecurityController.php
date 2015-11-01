<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class SecurityController extends Controller
{

    /**
     * @Route("/login", name="login_form")
     */
    public function loginAction(Request $request)
    {
        if($this->isGranted('ROLE_USER'))
            return $this->redirectToRoute('main');

        $captchaForm = $this->createFormBuilder()
            ->add('captcha','captcha',array(
                'label' => 'введите код с картинки: '
            ))->getForm();

        $captchaForm->handleRequest($request);

        if ($captchaForm->getErrors(true)->count() != 0) {
            $error = 'Код с картинки введён неверно';
            return $this->render(':user:login.html.twig', array('error' => $error,'captcha' => $captchaForm->createView()));
        }

        if($captchaForm->isValid()) {

            $user = $this->getDoctrine()->getRepository('AppBundle:User')->findOneBy(array('username' => $request->get('_username')));

            if($user == null) {
                $error = 'Пользователя не существует';
                return $this->render(':user:login.html.twig', array('error' => $error,'captcha' => $captchaForm->createView()));
            }

              if ($this->get('security.password_encoder')->isPasswordValid($user,$request->get('_password'))) {
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