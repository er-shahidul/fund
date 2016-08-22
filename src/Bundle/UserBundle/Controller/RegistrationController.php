<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bundle\UserBundle\Controller;

use Bundle\AppBundle\Controller\BaseController;
use Bundle\UserBundle\Entity\User;
use Bundle\UserBundle\Form\Type\UserType;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\UserBundle\Model\UserInterface;

/**
 * Controller managing the registration
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 * @author Christophe Coevoet <stof@notk.org>
 */
class RegistrationController extends BaseController
{
    public function registerAction(Request $request)
    {
        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->get('fos_user.registration.form.factory');
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $user = $userManager->createUser();
        $user->setEnabled(true);

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $formFactory->createForm();
        $form->setData($user);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);

            $userManager->updateUser($user);

            if (null === $response = $event->getResponse()) {
                $url = $this->generateUrl('fos_user_registration_confirmed');
                $response = new RedirectResponse($url);
            }

            $dispatcher->dispatch(FOSUserEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

            return $response;
        }

        return $this->render('FOSUserBundle:Registration:register.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * Tell the user to check his email provider
     */
    public function checkEmailAction()
    {
        $email = $this->get('session')->get('fos_user_send_confirmation_email/email');
        $this->get('session')->remove('fos_user_send_confirmation_email/email');
        $user = $this->get('fos_user.user_manager')->findUserByEmail($email);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('The user with email "%s" does not exist', $email));
        }

        return $this->render('FOSUserBundle:Registration:checkEmail.html.twig', array(
            'user' => $user,
        ));
    }

    /**
     * Receive the confirmation token from user email provider, login the user
     */
    public function confirmAction(Request $request, $token)
    {
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');

        $user = $userManager->findUserByConfirmationToken($token);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('The user with confirmation token "%s" does not exist', $token));
        }

        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $user->setConfirmationToken(null);
        $user->setEnabled(true);

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_CONFIRM, $event);

        $userManager->updateUser($user);

        if (null === $response = $event->getResponse()) {
            $url = $this->generateUrl('fos_user_registration_confirmed');
            $response = new RedirectResponse($url);
        }

        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_CONFIRMED, new FilterUserResponseEvent($user, $request, $response));

        return $response;
    }

    /**
     * Tell the user his account is now confirmed
     */
    public function confirmedAction()
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        return $this->render('FOSUserBundle:Registration:confirmed.html.twig', array(
            'user' => $user,
            'targetUrl' => $this->getTargetUrlFromSession(),
        ));
    }

    private function getTargetUrlFromSession()
    {
        // Set the SecurityContext for Symfony <2.6
        if (interface_exists('Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface')) {
            $tokenStorage = $this->get('security.token_storage');
        } else {
            $tokenStorage = $this->get('security.context');
        }

        $key = sprintf('_security.%s.target_path', $tokenStorage->getToken()->getProviderKey());

        if ($this->get('session')->has($key)) {
            return $this->get('session')->get($key);
        }
    }

    public function userProfileVerifyAction(Request $request) {

        if($this->isFacebookLogin()){
            return new Response(json_encode('hwi_oauth_service_redirect'));

        }
        $user = $this->getDoctrine()
                     ->getRepository('BundleUserBundle:User')
                     ->find($this->getUser()->getId());

        $form = $this->createForm(new UserType(null), $user);

        if ('POST' == $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {

                $user->setUsername($user->getUsername());
                $this->getDoctrine()->getRepository('BundleUserBundle:User')->update($user);

                $massage = 'Profile Successfully Updated';
                $this->get('session')->getFlashBag()->add('notice', $massage);
                return $this->redirect($this->generateUrl('campaign_list'));
            }
        }

        return $this->render(
            'BundleUserBundle:Profile:edit.html.twig',
            array(
                'form'     => $form->createView(),
                'user'     => $user
            )
        );
    }


    public function campaignUserVerifyAction(Request $request) {

        $tokenVerify  = $this->getUser()->getProfile()->getConfirmationTokenEmailVerify();
 
        if($tokenVerify == null) {
            $email = $request->request->get('fos_user_registration')['email'];
            $this->verificationEmail($email);
        }
        
        $phoneNumber = $request->request->get('fos_user_registration')['profile']['PhoneNumber'];

        $this->verificationPhone($phoneNumber);
        
        $user = $this->getDoctrine()
            ->getRepository('BundleUserBundle:User')
            ->find($this->getUser()->getId());

        $form = $this->createForm(new UserType(null), $user);

        return $this->render(
            'BundleUserBundle:Profile:confirmationCode.html.twig',
            array(
                'form'     => $form->createView(),
                'user'     => $user
            )
        );
    }
    public function campaignUserVerifiedAction(Request $request){


        $user = $this->getDoctrine()->getRepository('BundleUserBundle:User')->findOneBy(
            array('id' => $this->getUser()->getId())
        );
        if($user->getProfile()->getConfirmationTokenEmailVerify() != 1 ){

            $email = $request->request->all()['email'];

            if($email == $user->getProfile()->getConfirmationTokenEmail()) {

                $user->getProfile()->setConfirmationTokenEmailVerify(true);
                $this->getDoctrine()->getRepository('BundleUserBundle:User')->update($user);
            } else {
                $return = array("responseCode" => 203, "massage" => "Confirmation Email Code is Wrong");
                $return = json_encode($return);
                return new Response($return, 203, array('Content-Type' => 'application/json'));
            }
        }

        if($request->request->all()['phone'] == $user->getProfile()->getConfirmationTokenPhone()) {

            $user->getProfile()->setConfirmationTokenPhoneVerify(true);
            $this->getDoctrine()->getRepository('BundleUserBundle:User')->update($user);
        } else {
            $return = array("responseCode" => 203, "massage" => "Confirmation Phone Code is Wrong");
            $return = json_encode($return);
            return new Response($return, 203, array('Content-Type' => 'application/json'));
        }
       // return $this->redirect($this->generateUrl('organization_list'));
        $return = array("responseCode" => 202, "massage" => "verified");
        $return = json_encode($return);
        return new Response($return, 202, array('Content-Type' => 'application/json'));

    }

    public function generateNumber(){
        $six_digit_number = mt_rand(100000, 999999);
        return $six_digit_number;
    }
    public function sendEmail($email,$code)
    {

        $message = \Swift_Message::newInstance()
            ->setSubject('FUND Rising')
            ->setFrom('mirbahar@emicrograph.com')
            ->setTo($email->getEmail())
            ->setBody('your verification code is '. ' '.$code);

        return  $this->get('mailer')->send($message);

    }

    /**
     * @param $email
     * @return Response
     */
    private function verificationEmail($email)
    {

        $email            = $this->getDoctrine()->getRepository('BundleUserBundle:User')->findOneBy(
            array('email' => $email, 'id' => $this->getUser()->getId())
        );
        $verificationCode = $this->generateNumber();
        $email->getProfile()->setConfirmationTokenEmail($verificationCode);
        $this->getDoctrine()->getRepository('BundleUserBundle:User')->update($email);
        $this->sendEmail($email, $verificationCode);

        return $email;

    }
    /**
     * @param $phoneNumber
     * @return Response
     */
    private function verificationPhone($phoneNumber)
    {

        $phone            = $this->getDoctrine()->getRepository('BundleUserBundle:User')->findOneBy(
            array('id' => $this->getUser()->getId())
        );
//var_dump($phone);die;
        $verificationCode = $this->generateNumber();
        $phone->getProfile()->setConfirmationTokenPhone($verificationCode);
        $phone->getProfile()->setPhoneNumber($phoneNumber);
        $phone->getProfile()->setVerificationDateDuration(new \DateTime());
        $this->getDoctrine()->getRepository('BundleUserBundle:User')->update($phone);
        
        $message = 'your confirmation code is '.' '.$verificationCode;
        $this->get('sms.transporter')->setClient()
                                     ->setPhoneNumber($phoneNumber)
                                     ->setMessage($message)
                                     ->send();


    }

}
