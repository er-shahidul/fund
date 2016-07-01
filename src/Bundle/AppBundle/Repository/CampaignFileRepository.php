<?php

namespace Bundle\AppBundle\Repository;

use Bundle\AppBundle\Entity\CampaignFile;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * CampaignFileRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CampaignFileRepository extends EntityRepository
{
    public function create($data)
    {
        $this->_em->persist($data);
        $this->_em->flush();
        return $data;
    }
    public function filePersist($data)
    {
        $this->_em->persist($data);
    }
    public function saveCampaignFile($files,$campaign,$user){

        /** @var UploadedFile $file */

        foreach ($files as $file) {

            $campaignFile = new CampaignFile();

            $campaignFile->setFile($file);
            $campaignFile->upload();
            $campaignFile->setFileType($file->getClientMimeType());
            $campaignFile->setCreatedBy($user);
            $campaignFile->setFileName($file->getClientOriginalName());
            $campaignFile->setCampaign($campaign);
            $this->_em->persist($campaignFile);

        }
    }
}
