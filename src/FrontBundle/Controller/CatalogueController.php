<?php
/**
 * Created by PhpStorm.
 * User: Ny Avo
 * Date: 19/04/2018
 * Time: 13:36
 */
namespace FrontBundle\Controller;

use AppBundle\Entity\Produit;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CatalogueController
 */
class CatalogueController extends Controller
{
    /**
     * @Route("/produits", name="_challenge_front_produit_list")
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
}