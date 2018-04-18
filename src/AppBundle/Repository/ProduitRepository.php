<?php
/**
 * Created by PhpStorm.
 * User: nyavo
 * Date: 18/04/18
 * Time: 00:46
 */
namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class ProduitRepository
 */
class ProduitRepository extends EntityRepository
{
    public function getDataToArray()
    {
        $qb = $this->createQueryBuilder('p')
            ->select('p');

        return $qb->getQuery()->getArrayResult();
    }
}
