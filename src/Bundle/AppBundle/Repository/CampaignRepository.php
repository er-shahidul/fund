<?php

namespace Bundle\AppBundle\Repository;

use Bundle\AppBundle\Entity\Campaign;
use Bundle\AppBundle\Entity\Category;
use Doctrine\ORM\EntityRepository;

/**
 * CampaignRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CampaignRepository extends EntityRepository
{
    public function create($data)
    {
        
        $this->_em->persist($data);
        $this->_em->flush();
        return $data;
    }
    public function getSearchResult($data,$category = null)
    {
        $search = $data['search'];

        $query = $this->createQueryBuilder('cam');
        $query->leftJoin('cam.category', 'c');
        $query->leftJoin('cam.location', 'l');
        $query->where($query->expr()->like("cam.title", "'%$search%'"  ));
        $query->orWhere($query->expr()->like("l.name", "'%$search%'"  ));
        $query->orWhere($query->expr()->like("c.title", "'%$search%'"  ));
        if (!empty($category)){
            $query->andWhere('c.id = :category');
            $query->setParameter('category', $category);
        }
        $query->orderBy('cam.id', 'DESC');
 
        return $query->getQuery()->getResult();
    }
    public function countCampaignByCategories(Category $category)
    {

        $query = $this->createQueryBuilder('cam');
        $query->select('COUNT(cam.id) as camCount');
        $query->leftJoin('cam.category', 'c');
        $query->leftJoin('cam.location', 'l');
        $query->andWhere('c.id = :category');
        $query->setParameter('category', $category);

        return $query->getQuery()->getSingleScalarResult();
    }
     public function flashData($data){
         $this->_em->persist($data);
         $this->_em->flush();
         return $data;
     }
}
