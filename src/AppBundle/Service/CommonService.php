<?php
/**
 * Created by PhpStorm.
 * User: Ny Avo
 * Date: 18/04/2018
 * Time: 11:11
 */
namespace AppBundle\Service;

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
     *
     * @return mixed
     */
    public function getDataToArray($entityName)
    {
        $qb = $this->em->getRepository($entityName)->createQueryBuilder('t')
            ->select('t');

        return $qb->getQuery()->getArrayResult();
    }
}
