<?php
namespace Bundle\AppBundle\Service;

use Bundle\UserBundle\Entity\Profile;
use Bundle\UserBundle\Entity\User;
use FOS\UserBundle\Doctrine\UserManager;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider as BaseClass;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider;
use Symfony\Component\HttpFoundation\Session\Session;
/**
 * Class OAuthUserProvider
 * @package AppBundle\Security\Core\User
 */
class OAuthUserProvider extends BaseClass
{

    private $session;
    protected $userManager;


    /**
     * @param Session $session
     */

    public function __construct(Session $session,UserManager $userManager, $properties)
    {
        $this->session = $session;
        parent::__construct($userManager, $properties);
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


        if(empty($email) && empty($user)) {

            $this->session->set('profileImg',$response->getProfilepicture());
            $this->session->set('username', $username);
            $this->session->set('email',$email);
            $this->session->set('nickName',$response->getNickname());
            return false;
        }

        if($userByEmailAddress){

            $user = $this->updateProviderData($userByEmailAddress,$response);
            return $user;
        }
        if(empty($user)){
            $user = $this->storeProviderData($response->getUsername(),$response->getEmail(),$response->getProfilePicture(),$response->getNickname());
        }

        return $user;

    }

    public function storeProviderData($username, $email = null, $profilePic = null, $nickname= null){

        $entity = new User();
        $profile = new Profile();
        $filePath =  $this->UploadFacebookImage($profilePic);
        $profile->setPath($filePath);
        $profile->setFullName($nickname);
        $profile->setAddressLine('not now');

        $entity->setUsername($username);
        $entity->setEmail($email);

        $entity->setFacebook($username);
        $entity->setEnabled(true);
        $entity->setPassword(false);
        $profile->setIpAddress($this->getClientIpAddress());
        if(!empty($email)){
            $profile->setConfirmationTokenEmailVerify(true);
        }
        
        $entity->setProfile($profile);

        $this->userManager->updateUser($entity);
        return $entity;
    }
    public function updateProviderData(User  $userByEmailAddress,UserResponseInterface $response){

        $entity = $userByEmailAddress;
        $filePath =  $this->UploadFacebookImage($response->getProfilePicture());
        $entity->getProfile()->setPath($filePath);
        $entity->setUsername($response->getUsername());
        $entity->setEmail($response->getEmail());
        $entity->getProfile()->setFullName($response->getNickname());
        $entity->getProfile()->setIpAddress($this->getClientIpAddress());
        $entity->setEnabled(true);
        $this->userManager->updateUser($entity);
        return $entity;
    }

    public function getClientIpAddress(){

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    /**
     * @param $response
     * @param $entity
     * @return array
     */
    public function UploadFacebookImage($profilepicture)
    {
        $imageUrl = explode('?', $profilepicture);
        $imageUrl = explode('/', $imageUrl[0]);
        $imagePath = end($imageUrl);
        $filePath = 'uploads/profile/' . $imagePath;
        file_put_contents($filePath, file_get_contents($profilepicture));
        return $filePath;
    }

}