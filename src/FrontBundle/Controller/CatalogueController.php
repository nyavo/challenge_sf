<?php
/**
 * Created by PhpStorm.
 * User: Ny Avo
 * Date: 19/04/2018
 * Time: 13:36
 */
namespace FrontBundle\Controller;

use AppBundle\Entity\Commande;
use AppBundle\Entity\CommandeProduit;
use AppBundle\Entity\Produit;
use AppBundle\Form\Type\CommandType;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CatalogueController
 *
 * @Route("/catalogue")
 */
class CatalogueController extends Controller
{
    /**
     * @Route("/", name="_challenge_front_produit_list", options={"expose"=true})
     *
     * @return Response
     */
    public function listProduit()
    {
        return $this->render('FrontBundle:Produit:list.html.twig', array(
            'front' => true,
        ));
    }

    /**
     * @Route("/fiche/{id}", name="_challenge_front_fiche_produit", options={"expose"=true})
     *
     * @param Request $request
     * @param int     $id
     *
     * @return Response
     *
     * @throws EntityNotFoundException
     */
    public function ficheProduit(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $produit = $em->getRepository('AppBundle:Produit')->find($id);

        if (!$produit instanceof Produit) {
            throw new EntityNotFoundException();
        }

        return $this->render('FrontBundle:Produit:fiche.html.twig', array(
            'produit' => $produit,
        ));
    }

    /**
     * @Route("/getProduits", name="_challenge_get_produits", options={"expose"=true})
     */
    public function getProducts()
    {
        $data = $this->get('challenge.common.service')->getDataToArray('AppBundle:Produit');

        $response = new JsonResponse();

        $response->setData(
            array(
                'recordsTotal'    => count($data),
                'recordsFiltered' => count($data),
                'data'            => $data,
            )
        );

        return $response;
    }
}