<?php
/**
 * Created by PhpStorm.
 * User: nyavo
 * Date: 17/04/18
 * Time: 23:18
 */
namespace AppBundle\Controller;

use AppBundle\Entity\Produit;
use AppBundle\Form\Type\ProduitType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ProduitController
 *
 * @Route("/produit")
 */
class ProduitController extends Controller
{
    /**
     * @Route("/list", name="_challenge_produit_list")
     *
     * @return Response
     */
    public function listProduit()
    {
        return $this->render('AppBundle:Produit:list.html.twig');
    }

    /**
     * @Route("/add", name="_challenge_produit_add")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function addProduit(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em->persist($produit);
                $em->flush();
                $this->addFlash('success', 'Ajout réussi');
            } catch (\Exception $exc) {
                $this->addFlash('error', $exc->getMessage());
            }

            return $this->redirect($this->generateUrl('_challenge_produit_list'));
        }

        return $this->render('AppBundle:Produit:create.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}

