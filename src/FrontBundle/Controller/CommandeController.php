<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 19/04/18
 * Time: 23:19
 */
namespace FrontBundle\Controller;

use AppBundle\Entity\Commande;
use AppBundle\Entity\CommandeProduit;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CommandeController
 *
 * @Route("/commande")
 */
class CommandeController extends Controller
{

    /**
     * @Route("/", name="_challenge_front_commande", options={"expose"=true})
     *
     * @return Response
     */
    public function listCommande()
    {
        return $this->render('FrontBundle:Commande:list.html.twig');
    }

    /**
     * @Route("/liste", name="_challenge_front_commande_list", options={"expose"=true})
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function getCommandList()
    {
        $commonService = $this->get('challenge.common.service');

        $data = $commonService->ficheCommande();

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

    /**
     * @Route("/detail/{id}", name="_challenge_front_commande_detail", options={"expose"=true})
     *
     * @param Request $request
     * @param int     $id
     *
     * @return Response
     *
     * @throws EntityNotFoundException
     */
    public function detailCommande(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $commande = $em->getRepository('AppBundle:Commande')->find($id);

        if (!$commande instanceof Commande) {
            throw new EntityNotFoundException();
        }

        return $this->render('FrontBundle:Commande:detail.html.twig', array(
            'commande' => $commande,
        ));
    }

    /**
     * @Route("/produits/{id}", name="_challenge_front_commande_produits", options={"expose"=true})
     *
     * @param Request $request
     * @param int     $id
     *
     * @return JsonResponse
     *
     * @throws EntityNotFoundException
     * @throws \Exception
     */
    public function getProduits(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $commande = $em->getRepository('AppBundle:Commande')->find($id);

        if (!$commande instanceof Commande) {
            throw new EntityNotFoundException();
        }

        $data = array();
        foreach ($commande->getProduits() as $produit) {
            $data[] = array(
                'titre' => $produit->getProduit()->getTitre(),
                'quantite' => $produit->getQuantiteCommande(),
                'prix' => $produit->getProduit()->getPrix(),
                'total' => $produit->getProduit()->getPrix() * $produit->getQuantiteCommande(),
            );
        }

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