<?php
/**
 * Created by PhpStorm.
 * User: rahat
 * Date: 12/7/15
 * Time: 12:08 PM
 */

namespace Bundle\UserBundle\Service;


use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class AuthenticationHandler  implements AuthenticationSuccessHandlerInterface, AuthenticationFailureHandlerInterface
{
    private $router;
    private $session;
    private $translator;

    /**
     * @param RouterInterface $router
     * @param Session $session
     * @param Translator $translator
     */

    public function __construct( RouterInterface $router, Session $session, $translator)
    {
        $this->router  = $router;
        $this->session = $session;
        $this->translator = $translator;
    }


    /**
     * @param Request $request
     * @param TokenInterface $token
     * @return RedirectResponse|Response
     */

    public function onAuthenticationSuccess( Request $request, TokenInterface $token )
    {
        // if AJAX login
        if ( $request->isXmlHttpRequest() ) {

            $array = array( 'success' => true ); // data to return via JSON

            return new JsonResponse($array);

        }else{

            return new RedirectResponse('/');
        }
    }

    public function onAuthenticationFailure( Request $request, AuthenticationException $exception )
    {
        // if AJAX login
        if ( $request->isXmlHttpRequest() ) {
            $array = array('success' => false, 'message' => $this->translator->trans($exception->getMessage())); // data to return via JSON
            return new JsonResponse($array);

        }else{
            $request->getSession()->set(SecurityContextInterface::AUTHENTICATION_ERROR, $exception);
            return new RedirectResponse( $this->router->generate( 'fos_user_security_login' ) );
        }
    }

}