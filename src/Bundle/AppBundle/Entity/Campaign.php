<?php

namespace Bundle\AppBundle\Entity;

use Bundle\UserBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;



/**
 * Campaign
 *
 * @ORM\Table(name="campaign")
 * @ORM\Entity(repositoryClass="Bundle\AppBundle\Repository\CampaignRepository")
 * @Vich\Uploadable
 */
class Campaign 
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="float")
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdDate", type="datetime")
     */
    private $createdDate;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Bundle\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="created_by", nullable=true)
     */
    private $createdBy;

    /**
     * @var Category
     *
     * @ORM\ManyToOne(targetEntity="Bundle\AppBundle\Entity\Category")
     * @ORM\JoinColumn(name="category", nullable = true)
     */
    private $category;

    /**
     * @var Organization
     *
     * @ORM\ManyToOne(targetEntity="Bundle\AppBundle\Entity\Organization")
     * @ORM\JoinColumn(name="organization", nullable=true)
     */
    private $organization;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Bundle\AppBundle\Entity\CampaignDetails", mappedBy="Campaign", cascade={"persist"})
     */
    private $campaignDetails;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Bundle\AppBundle\Entity\CampaignComment", mappedBy="Campaign", cascade={"persist"})
     */
    private $campaignComments;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="campaign_file", fileNameProperty="campaignFileName")
     *
     * @var File
     */
    private $campaignFile;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean", length=15)
     */
    private $status = false;

    /**
     * @return File
     */
    public function getCampaignFile()
    {
        return $this->campaignFile;
    }

    /**
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $campaignFile
     * @return Campaign
     */
    public function setCampaignFile(File $campaignFile = null)
    {
        $this->campaignFile = $campaignFile;
        return $this;
    }

    /**
     * @return string
     */
    public function getCampaignFileName()
    {
        return $this->campaignFileName;
    }

    /**
     * @param string $campaignFileName
     * @return Campaign
     */
    public function setCampaignFileName($campaignFileName)
    {
        $this->campaignFileName = $campaignFileName ;

        return $this;
    }

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private $campaignFileName;


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
     * Set title
     *
     * @param string $title
     * @return Campaign
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set amount
     *
     * @param float $amount
     * @return Campaign
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return float 
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Campaign
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     * @return Campaign
     */
    public function setCreatedDate($createdDate)
    {
        $this->createdDate = $createdDate;

        return $this;
    }

    /**
     * Get createdDate
     *
     * @return \DateTime 
     */
    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    /**
     * @return mixed
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param mixed $users
     */
    public function setUsers($users)
    {
        $this->users = $users;
    }

    /**
     * @return mixed
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @param mixed $createdBy
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
    }

    /**
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param Category $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * @return Organization
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * @param Organization $organization
     */
    public function setOrganization($organization)
    {
        $this->organization = $organization;
    }

    /**
     * @return ArrayCollection
     */
    public function getCampaignDetails()
    {
        return $this->campaignDetails;
    }

    /**
     * @param ArrayCollection $campaignDetails
     */
    public function setCampaignDetails($campaignDetails)
    {
        $this->campaignDetails = $campaignDetails;
    }

    /**
     * @return ArrayCollection
     */
    public function getCampaignComments()
    {
        return $this->campaignComments;
    }

    /**
     * @param ArrayCollection $campaignComments
     */
    public function setCampaignComments($campaignComments)
    {
        $this->campaignComments = $campaignComments;
    }

    /**
     * @return boolean
     */
    public function isStatus()
    {
        return $this->status;
    }

    /**
     * @param boolean $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }
}
