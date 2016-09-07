<?php
namespace Bundle\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="profiles")
 * @ORM\HasLifecycleCallbacks
 */
class Profile
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="full_name", type="string", length=255 , nullable=true)
     * @Assert\NotBlank()
     */
    private $fullName;
    
    /**
     * @var string
     *
     * @ORM\Column(name="phone_number", type="string", length=255 , nullable = true)
     * @Assert\NotBlank()
     */
    private $PhoneNumber; 
    
    /**
     * @var string
     *
     * @ORM\Column(name="confirmation_token_phone", type="string", length=255 , nullable = true)
     * @Assert\NotBlank()
     */
    private $confirmationTokenPhone;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="verification_date_duration", type="datetime", nullable = true)
     * @Assert\NotBlank()
     */
    private $verificationDateDuration;

    /**
     * @return string
     */
    public function getConfirmationTokenPhone()
    {
        return $this->confirmationTokenPhone;
    }

    /**
     * @param string $confirmationTokenPhone
     */
    public function setConfirmationTokenPhone($confirmationTokenPhone)
    {
        $this->confirmationTokenPhone = $confirmationTokenPhone;
    }

    /**
     * @return mixed
     */
    public function getConfirmationTokenEmail()
    {
        return $this->confirmationTokenEmail;
    }

    /**
     * @param mixed $confirmationTokenEmail
     */
    public function setConfirmationTokenEmail($confirmationTokenEmail)
    {
        $this->confirmationTokenEmail = $confirmationTokenEmail;
    }

    /**
     * @return mixed
     */
    public function getConfirmationTokenPhoneVerify()
    {
        return $this->confirmationTokenPhoneVerify;
    }

    /**
     * @param mixed $confirmationTokenPhoneVerify
     */
    public function setConfirmationTokenPhoneVerify($confirmationTokenPhoneVerify)
    {
        $this->confirmationTokenPhoneVerify = $confirmationTokenPhoneVerify;
    }

    /**
     * @return mixed
     */
    public function getConfirmationTokenEmailVerify()
    {
        return $this->confirmationTokenEmailVerify;
    }

    /**
     * @param mixed $confirmationTokenEmailVerify
     */
    public function setConfirmationTokenEmailVerify($confirmationTokenEmailVerify)
    {
        $this->confirmationTokenEmailVerify = $confirmationTokenEmailVerify;
    }
    
    /**
     * @var string
     *
     * @ORM\Column(name="confirmation_token_email", type="string", length=255 , nullable = true)
     * @Assert\NotBlank()
     */
    private $confirmationTokenEmail;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="confirmation_token_phone_verify", type="boolean", nullable = true)
     * @Assert\NotBlank()
     */
    private $confirmationTokenPhoneVerify;  
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="confirmation_token_email_verify", type="boolean",  nullable = true)
     * @Assert\NotBlank()
     */
    private $confirmationTokenEmailVerify;

    /**
     * @var string
     *
     * @ORM\Column(name="addressLine", type="string", length=255 , nullable=true)
     * @Assert\NotBlank()
     */
    private $addressLine;
    
    /**
     * @ORM\OneToOne(targetEntity="User", inversedBy="profile")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id", unique = true, onDelete="CASCADE")
     * })
     */
    protected $user;

    /**
     * @Assert\File(maxSize="5M")
     */
    public $fileName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $path;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ipAddress;

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @param string $fileName
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * @param mixed $mimeType
     */
    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;
    }

    /**
     * @return mixed
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param mixed $size
     */
    public function setSize($size)
    {
        $this->size = $size;
    }


    private $mimeType;


    private $size;

    /**
     * @Assert\File(maxSize="6000000")
     */
    private $file;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set fullName
     *
     * @param string $fullName
     * @return Profile
     */
    public function setFullName($fullName)
    {
        $this->fullName = $fullName;

        return $this;
    }

    /**
     * Get fullName
     *
     * @return string 
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * Set addressLine
     *
     * @param string $addressLine
     * @return Profile
     */
    public function setAddressLine($addressLine)
    {
        $this->addressLine = $addressLine;

        return $this;
    }

    /**
     * Get addressLine
     *
     * @return string 
     */
    public function getAddressLine()
    {
        return $this->addressLine;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $users
     */
    public function setUser($user)
    {
        $this->user = $user;

    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    public function userUplaodCallBack(){

    }

    /**
     * @return mixed
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param mixed $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

    /**
     * @return mixed
     */
    public function getIpAddress()
    {
        return $this->ipAddress;
    }

    /**
     * @param mixed $ipAddress
     */
    public function setIpAddress($ipAddress)
    {
        $this->ipAddress = $ipAddress;
    }

    /**
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->PhoneNumber;
    }

    /**
     * @param string $PhoneNumber
     */
    public function setPhoneNumber($PhoneNumber)
    {
        $this->PhoneNumber = $PhoneNumber;
    }

    /**
     * @return \DateTime
     */
    public function getVerificationDateDuration()
    {
        return $this->verificationDateDuration;
    }

    /**
     * @param \DateTime $verificationDateDuration
     */
    public function setVerificationDateDuration($verificationDateDuration)
    {
        $this->verificationDateDuration = $verificationDateDuration;
    }
}
