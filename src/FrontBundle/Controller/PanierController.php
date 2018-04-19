<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 19/04/18
 * Time: 23:17
 */
namespace FrontBundle\Controller;

use AppBundle\Entity\Commande;
use AppBundle\Entity\CommandeProduit;
use AppBundle\Form\Type\CommandType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PanierController
 *
 * @Route("/panier")
 */
class PanierController extends Controller
{
    /**
     * @Route("/", name="_challenge_front_voir_panier", options={"expose"=true})
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
     * @Route("/save", name="_challenge_front_panier_save", options={"expose"=true})
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
}