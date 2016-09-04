<?php

namespace Bundle\AppBundle\Entity;

use Bundle\UserBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Campaign
 *
 * @ORM\Table(name="campaign")
 * @ORM\Entity(repositoryClass="Bundle\AppBundle\Repository\CampaignRepository")
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
     * @var string
     *
     * @ORM\Column(name="campaign_video_url", type="string", length=255, nullable=true)
     */
    private $campaignVideoUrl;

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
     * @var \DateTime
     *
     * @ORM\Column(name="updatedDate", type="datetime", nullable=true)
     */
    private $updatedDate;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_of_campaign_date", type="datetime", nullable=true)
     */
    private $endOfCampaignDate;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Bundle\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="created_by", nullable=true)
     */
    private $createdBy;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Bundle\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="updated_by", nullable=true)
     */
    private $updatedBy;

    /**
     * @var Category
     *
     * @ORM\ManyToOne(targetEntity="Bundle\AppBundle\Entity\Category")
     * @ORM\JoinColumn(name="category", nullable = true)
     */
    private $category;
    
    /**
     * @var Location
     *
     * @ORM\ManyToOne(targetEntity="Bundle\AppBundle\Entity\Location")
     * @ORM\JoinColumn(name="location", nullable = true)
     */
    private $location;

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
     * @ORM\OneToMany(targetEntity="Bundle\AppBundle\Entity\Donation", mappedBy="Campaign", cascade={"persist"})
     */
    private $donationDetails;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Bundle\AppBundle\Entity\CampaignComment", mappedBy="Campaign", cascade={"persist"})
     */
    private $campaignComments;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Bundle\AppBundle\Entity\CampaignFile", mappedBy="campaign")
     */
    private $campaignFiles;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean", length=15)
     */
    private $status = false;    
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="feature", type="boolean", length=15)
     */
    private $feature = false;  
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="verify", type="boolean", length=15, nullable=true)
     */
    private $verify = true;
    

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

    /**
     * @return Location
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param Location $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * @return \DateTime
     */
    public function getEndOfCampaignDate()
    {
        return $this->endOfCampaignDate;
    }

    /**
     * @param \DateTime $endOfCampaignDate
     */
    public function setEndOfCampaignDate($endOfCampaignDate)
    {
        $this->endOfCampaignDate = $endOfCampaignDate;
    }

    /**
     * @return ArrayCollection
     */
    public function getCampaignFiles()
    {
        return $this->campaignFiles;
    }

    /**
     * @param ArrayCollection $campaignFiles
     */
    public function setCampaignFiles($campaignFiles)
    {
        $this->campaignFiles = $campaignFiles;
    }

    /**
     * @return User
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }

    /**
     * @param User $updatedBy
     */
    public function setUpdatedBy($updatedBy)
    {
        $this->updatedBy = $updatedBy;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedDate()
    {
        return $this->updatedDate;
    }

    /**
     * @param \DateTime $updatedDate
     */
    public function setUpdatedDate($updatedDate)
    {
        $this->updatedDate = $updatedDate;
    }

    /**
     * @return string
     */
    public function getCampaignVideoUrl()
    {
        return $this->campaignVideoUrl;
    }

    /**
     * @param string $campaignVideoUrl
     */
    public function setCampaignVideoUrl($campaignVideoUrl)
    {
        $this->campaignVideoUrl = $campaignVideoUrl;
    }

    /**
     * @return ArrayCollection
     */
    public function getDonationDetails()
    {
        return $this->donationDetails;
    }

    /**
     * @param ArrayCollection $donationDetails
     */
    public function setDonationDetails($donationDetails)
    {
        $this->donationDetails = $donationDetails;
    }

    /**
     * @return boolean
     */
    public function isFeature()
    {
        return $this->feature;
    }

    /**
     * @param boolean $feature
     */
    public function setFeature($feature)
    {
        $this->feature = $feature;
    }

    public function getRemainingDays(){
        $days =   date_diff(new \DateTime(), new \DateTime($this->getEndOfCampaignDate()->format('Y-m-d')));
        return $days->days;
    }

    /**
     * @return boolean
     */
    public function isVerify()
    {
        return $this->verify;
    }

    /**
     * @param boolean $verify
     */
    public function setVerify($verify)
    {
        $this->verify = $verify;
    }
}
