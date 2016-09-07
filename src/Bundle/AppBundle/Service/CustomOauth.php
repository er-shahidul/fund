<?php
/**
 * Created by PhpStorm.
 * User: rahat
 * Date: 8/13/15
 * Time: 1:39 PM
 */

namespace Bundle\AppBundle\Service;
use Bundle\UserBundle\Entity\Profile;
use Bundle\UserBundle\Entity\User;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\Exception\AccountNotLinkedException;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider as baseFosuserProvider;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;

class CustomOauth extends  baseFosuserProvider{

    /**
     * take session service
     * @var Session
     */
    private  $session;

    public function setSession(Session $session){
        $this->session = $session;
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $username = $response->getUsername();
        $email = $response->getEmail();
        $user = $this->userManager->findUserBy(array($this->getProperty($response) => $username));
        
        $userByEmailAddress = $this->userManager->findUserByEmail($email);

        if(empty($email) && empty($user)){
            $this->getSession()->set('profileImg',$response->getProfilepicture());
            $this->getSession()->set('username', $username);
            $this->getSession()->set('email',$email);
            $this->getSession()->set('nickName',$response->getNickname());
            return false;
        }

       if($userByEmailAddress){
           $user = $this->updateProviderData($userByEmailAddress);
           return $user;
       }
        if(empty($user)){
            $user = $this->storeProviderData($response->getUsername(),$response->getEmail(),$response->getProfilePicture(),$response->getNickname());
        }

        return $user;
    }

    public function storeProviderData($username, $email = null, $profilePic=null,$nickname= null){

        $entity = new User();
        $profile = new Profile();
        $filePath =  $this->UploadFacebookImage($profilePic, $entity);
        $profile->setPath($filePath);
        $profile->setFullName($nickname);
        $profile->setAddressLine('not now');

        $entity->setUsername($username);
        $entity->setEmail($email);
        $entity->setFacebook($username);
        $entity->setEnabled(true);
        $entity->setPassword(false);
        $entity->setProfile($profile);

        $this->userManager->updateUser($entity);
        return $entity;
    }
    public function updateProviderData($userByEmailAddress){
        $entity = $userByEmailAddress;
        $facebook = $userByEmailAddress->getUsername();
        $entity->setFacebook($facebook);
        $entity->setEnabled(true);
        $this->userManager->updateUser($entity);
        return $entity;
    }

    /**
     * @param $response
     * @param $entity
     * @return array
     */
    public function UploadFacebookImage($profilepicture, $entity)
    {
        $imageUrl = explode('?', $profilepicture);
        $imageUrl = explode('/', $imageUrl[0]);
        $imagePath = end($imageUrl);
        $filePath = 'uploads/profile/' . $imagePath;
        file_put_contents($filePath, file_get_contents($profilepicture));
        return $filePath;
    }

    /**
     * @return mixed|Session
     */
    public function getSession()
    {
        return $this->session;
    }


} 