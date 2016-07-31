<?php

namespace Bundle\AppBundle\Entity;

use Bundle\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * CampaignDetails
 *
 * @ORM\Table(name="campaign_details")
 * @ORM\Entity(repositoryClass="Bundle\AppBundle\Repository\CampaignDetailsRepository")
 * @ORM\HasLifecycleCallbacks
 */
class CampaignDetails
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
     * @ORM\Column(name="notes", type="text", nullable=true)
     */
    private $notes;
    /**
     * @var string
     *
     * @ORM\Column(name="campaign_detail_video_url", type="text", nullable=true)
     */
    private $campaignDetailVideoUrl;

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
     * @ORM\ManyToOne(targetEntity="Bundle\AppBundle\Entity\Campaign", inversedBy="campaignDetails", cascade={"persist"})
     * @ORM\JoinColumn(name="campaign", nullable=true)
     */
    private $campaign;

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
     * @param mixed $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }


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
     * Set createdDate
     *
     * @param \DateTime $createdDate
     * @return CampaignDetails
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
     * @return string
     */
    public function getCampaignDetailVideoUrl()
    {
        return $this->campaignDetailVideoUrl;
    }

    /**
     * @param string $campaignDetailVideoUrl
     */
    public function setCampaignDetailVideoUrl($campaignDetailVideoUrl)
    {
        $this->campaignDetailVideoUrl = $campaignDetailVideoUrl;
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
     * Get path
     *
     * @return string
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
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
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
    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/campaign/';
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
}
