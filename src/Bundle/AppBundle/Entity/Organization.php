<?php

namespace Bundle\AppBundle\Entity;

use Bundle\UserBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Organization
 *
 * @ORM\Table(name="organization")
 * @ORM\Entity(repositoryClass="Bundle\AppBundle\Repository\OrganizationRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Organization
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255, nullable=true)
     */
    private $address;
    
    /**
     * @var string
     *
     * @ORM\Column(name="about_organization", type="text", nullable=true)
     */
    private $aboutOrganization;

    /**
     * @var string
     *
     * @ORM\Column(name="mobileNumber", type="string", length=255, nullable=true)
     */
    private $mobileNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=15, nullable=true)
     */
    private $status;
    
    /**
     * @var string
     *
     * @ORM\Column(name="validateOrganization", type="string", length=255, nullable=true)
     */
    private $validateOrganization;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Bundle\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="created_by", nullable=true)
     */
    private $createdBy;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Bundle\AppBundle\Entity\Campaign", mappedBy="Organization", cascade={"persist"})
     */
    private $campaign;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdDate", type="datetime")
     */
    private $createdDate;

    /**
     * @var string
     *
     * @ORM\Column(name="file_name", type="string", length=255, nullable=true)
     */
    private $fileName;

    /**
     * @var string
     *
     * @ORM\Column(name="file_type", type="text", nullable= true )
     */
    private $fileType;

    /**
     * @Assert\File(maxSize="6000000")
     */

    public $file;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $path;


    public $temp;


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
     * Set name
     *
     * @param string $name
     * @return Organization
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set address
     *
     * @param string $address
     * @return Organization
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string 
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set mobileNumber
     *
     * @param string $mobileNumber
     * @return Organization
     */
    public function setMobileNumber($mobileNumber)
    {
        $this->mobileNumber = $mobileNumber;

        return $this;
    }

    /**
     * Get mobileNumber
     *
     * @return string 
     */
    public function getMobileNumber()
    {
        return $this->mobileNumber;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Organization
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Organization
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @param User $createdBy
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    /**
     * @param \DateTime $createdDate
     */
    public function setCreatedDate($createdDate)
    {
        $this->createdDate = $createdDate;
    }

    /**
     * @return ArrayCollection
     */
    public function getCampaign()
    {
        return $this->campaign;
    }

    /**
     * @param ArrayCollection $campaign
     */
    public function setCampaign($campaign)
    {
        $this->campaign = $campaign;
    }

    /**
     * @return string
     */
    public function getValidateOrganization()
    {
        return $this->validateOrganization;
    }

    /**
     * @param string $validateOrganization
     */
    public function setValidateOrganization($validateOrganization)
    {
        $this->validateOrganization = $validateOrganization;
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {

        if (null === $this->getFile()) {
            return;
        }
        $this->preUpload();

        $this->getFile()->move($this->getUploadRootDir(), $this->path);

        $this->file = null;
    }

    protected function getUploadRootDir()
    {

        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__ . '/../../../../web/' . $this->getUploadDir();
    }
    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {

        if (null !== $this->getFile()) {
            // do whatever you want to generate a unique name
            $filename = sha1(uniqid(mt_rand(), true));

            $this->path = $filename . '.' . $this->getFile()->guessExtension();

        }

    }

    protected function getUploadDir()
    {

        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/organization/';
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if ($file = $this->getAbsolutePath()) {
            unlink($file);
        }
    }

    public function getAbsolutePath()
    {

        return null === $this->path
            ? null
            : $this->getUploadRootDir() . '/' . $this->path;
    }

    public function getWebPath()
    {
        return null === $this->path
            ? null
            : $this->getUploadDir() . '/' . $this->path;
    }

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
     * @return string
     */
    public function getFileType()
    {
        return $this->fileType;
    }

    /**
     * @param string $fileType
     */
    public function setFileType($fileType)
    {
        $this->fileType = $fileType;
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
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param mixed $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getAboutOrganization()
    {
        return $this->aboutOrganization;
    }

    /**
     * @param string $aboutOrganization
     */
    public function setAboutOrganization($aboutOrganization)
    {
        $this->aboutOrganization = $aboutOrganization;
    }
}
