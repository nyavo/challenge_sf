<?php
/**
 * Created by PhpStorm.
 * User: Ny Avo
 * Date: 18/04/2018
 * Time: 11:11
 */
namespace AppBundle\Service;

use AppBundle\Entity\BonEntree;
use AppBundle\Entity\BonSortie;
use AppBundle\Entity\Produit;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class CommonService
 */
class CommonService
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(ContainerInterface $container, EntityManager $em)
    {
        $this->container = $container;
        $this->em = $em;
    }

    /**
     * @param string $entityName
     * @param array  $criteria
     * @param array  $params
     *
     * @return mixed
     */
    public function getDataToArray($entityName, $criteria = array(), $params = array())
    {
        $qb = $this->em->getRepository($entityName)->createQueryBuilder('t')
            ->select('t');
        foreach ($criteria as $criterion) {
            $qb->andWhere('t.'.$criterion);
        }

        foreach ($params as $key => $param) {
            $qb->setParameter($key, $param);
        }

        return $qb->getQuery()->getArrayResult();
    }

    /**
     * @param Produit $produit
     * @param mixed   $flux
     * @param bool    $update
     */
    public function saveFlux($produit, $flux, $update = false)
    {
        $conn = $this->em->getConnection();

        try {
            $conn->beginTransaction();
            $this->em->persist($flux);
            $this->em->flush();
            if (!$update) {
                if ($flux instanceof BonEntree) {
                    $produit->setStock($produit->getStock() + $flux->getQte());
                } else if ($flux instanceof BonSortie) {
                    $produit->setStock($produit->getStock() - $flux->getQte());
                }
                $this->em->persist($produit);
                $this->em->flush();
            }

            $conn->commit();
        } catch (\Exception $e) {
            $conn->rollback();
        }
    }
}
