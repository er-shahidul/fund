<?php

namespace Bundle\AppBundle\Entity;

use Bundle\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Donation
 *
 * @ORM\Table(name="Donation")
 * @ORM\Entity(repositoryClass="Bundle\AppBundle\Repository\DonationRepository")
 */
class Donation
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
     * @ORM\Column(name="first_name", type="string", length=255, nullable = true)
     */
    private $first_name;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=255, nullable = true)
     */
    private $last_name;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable = true)
     */
    private $email;
    
    /**
     * @var int
     *
     * @ORM\Column(name="postal_code", type="integer", nullable = true)
     */
    private $postalCode;

    /**
     * @var float
     *
     * @ORM\Column(name="donate_amount", type="float")
     */
    private $donate_amount;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $notes;

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
     * @var Campaign
     *
     * @ORM\ManyToOne(targetEntity="Bundle\AppBundle\Entity\Campaign", inversedBy="donationDetails", cascade={"persist"})
     * @ORM\JoinColumn(name="campaign", nullable=true)
     */
    private $campaign;

    /**
     * @var boolean
     *
     * @ORM\Column(name="anonymous", type="boolean", length=15)
     */
    private $anonymous = false;
    

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
     * @return string
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * @param string $first_name
     */
    public function setFirstName($first_name)
    {
        $this->first_name = $first_name;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->last_name;
    }

    /**
     * @param string $last_name
     */
    public function setLastName($last_name)
    {
        $this->last_name = $last_name;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return int
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * @param int $postalCode
     */
    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;
    }

    /**
     * @return float
     */
    public function getDonateAmount()
    {
        return $this->donate_amount;
    }

    /**
     * @param float $donate_amount
     */
    public function setDonateAmount($donate_amount)
    {
        $this->donate_amount = $donate_amount;
    }

    /**
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @param string $notes
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;
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
     * @return Campaign
     */
    public function getCampaign()
    {
        return $this->campaign;
    }

    /**
     * @param Campaign $campaign
     */
    public function setCampaign($campaign)
    {
        $this->campaign = $campaign;
    }

    /**
     * @return boolean
     */
    public function isAnonymous()
    {
        return $this->anonymous;
    }

    /**
     * @param boolean $anonymous
     */
    public function setAnonymous($anonymous)
    {
        $this->anonymous = $anonymous;
    }
    
}
