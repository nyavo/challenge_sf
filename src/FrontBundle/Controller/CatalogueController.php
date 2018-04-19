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
 */
class CatalogueController extends Controller
{
    /**
     * @Route("/produits", name="_challenge_front_produit_list", options={"expose"=true})
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
    /**
     * @Route("/panier", name="_challenge_front_voir_panier", options={"expose"=true})
     *
     * @return Response
     */
    public function voirPanier()
    {
        $form = $this->createForm(CommandType::class, new Commande());

        return $this->render('FrontBundle:Produit:panier.html.twig', array(
            "form" => $form->createView(),
        ));
    }

    /**
     * @Route("/panier/save", name="_challenge_front_panier_save", options={"expose"=true})
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function savePanier(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $conn = $em->getConnection();
        $commande = new Commande();
        $form = $this->createForm(CommandType::class, $commande);

        $batchSize = $this->getParameter('batch_size');

        $form->handleRequest($request);

        $dataReturn = array(
            'success' => false,
        );

        if ($form->isSubmitted() && $form->isValid()) {
            $commande->setClient($this->getUser());
            $commande->setDateCommande(new \DateTime());

            try {
                $conn->beginTransaction();
                $em->persist($commande);
                $em->flush();

                $produits = $request->get('produits');
                $i = 0;
                foreach ($produits as $produitId => $quantite) {
                    $oProduit = $em->getRepository('AppBundle:Produit')->find($produitId);
                    $commandeProduit = new CommandeProduit();
                    $commandeProduit->setCommande($commande);
                    $commandeProduit->setProduit($oProduit);
                    $commandeProduit->setQuantiteCommande($quantite);

                    $em->persist($commandeProduit);
                    $i++;
                    if ($i % $batchSize == 0) {
                        $em->flush();
                        $em->clear();
                    }
                }
                $em->flush();
                $em->clear();
                $conn->commit();
                $dataReturn['success'] = true;
                $this->addFlash('success', 'Votre commande est enregistrée.');
            } catch (\Exception $exc) {
                $conn->rollback();
                $this->addFlash('error', "Votre commande n'a pas pu être enregistrée.");
            }
        }

        $response = new JsonResponse();
        $response->setData($dataReturn);

        return $response;
    }

    /**
     * @Route("/commande", name="_challenge_front_commande", options={"expose"=true})
     *
     * @return Response
     */
    public function listCommande()
    {
        return $this->render('FrontBundle:Commande:list.html.twig');
    }

    /**
     * @Route("/commande/liste", name="_challenge_front_commande_list", options={"expose"=true})
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
}