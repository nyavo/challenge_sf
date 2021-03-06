<?php
/**
 * Created by PhpStorm.
 * User: nyavo
 * Date: 17/04/18
 * Time: 23:18
 */
namespace AppBundle\Controller;

use AppBundle\Entity\BonEntree;
use AppBundle\Entity\BonSortie;
use AppBundle\Entity\Produit;
use AppBundle\Form\Type\FluxType;
use AppBundle\Form\Type\ProduitType;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * @Route("/add", name="_challenge_produit_add", options={"expose"=true})
     * @Route("/update/{id}", name="_challenge_produit_update", options={"expose"=true})
     *
     * @param Request $request
     *
     * @param null    $id
     *
     * @return Response
     *
     * @throws EntityNotFoundException
     */
    public function addOrUpdateProduit(Request $request, $id = null)
    {
        $em = $this->getDoctrine()->getManager();
        $produit = is_null($id) ? new Produit() : $em->getRepository('AppBundle:Produit')->find($id);

        if (!$produit instanceof Produit) {
            throw new EntityNotFoundException();
        }
        $form = $this->createForm(ProduitType::class, $produit);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em->persist($produit);
                $em->flush();
                $message = $request->get('_route') == '_challenge_produit_add' ? 'Ajout réussi' : 'Modification réussi';
                $this->addFlash('success', $message);
            } catch (\Exception $exc) {
                $this->addFlash('error', $exc->getMessage());
            }

            return $this->redirect($this->generateUrl('_challenge_produit_list'));
        }

        return $this->render('AppBundle:Produit:create.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/remove/{id}", name="_challenge_remove_product", options={"expose"=true})
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    public function remove($id)
    {
        $em = $this->getDoctrine()->getManager();

        $produit = $em->getRepository('AppBundle:Produit')->find($id);
        $dataReturn = array(
            'success' => false,
            'message' => '',
        );
        if ($produit instanceof Produit) {
            try {
                $em->remove($produit);
                $em->flush();
                $dataReturn['success'] = true;
                $dataReturn['message'] = 'Suppression réussie';
            } catch (\Exception $exc) {
                $dataReturn['message'] = $exc->getMessage();
            }
        } else {
            $dataReturn['message'] = 'Ce produit a déjà été supprimé';
        }
        $response = new JsonResponse();
        $response->setData($dataReturn);

        return $response;
    }

    /**
     * @Route("/flux", name="_challenge_produit_flux", options={"expose"=true})
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws EntityNotFoundException
     */
    public function flux(Request $request)
    {
        $produitId = $request->get('id');

        $produit = $this->getDoctrine()->getRepository('AppBundle:Produit')->find($produitId);

        if (!$produit instanceof Produit) {
            throw new EntityNotFoundException();
        }
        $type = $request->get('type');

        return $this->render('AppBundle:Flux:list.html.twig', array(
            'produit' => $produit,
            'type' => $type,
        ));
    }

    /**
     * @Route("/flux/list", name="_challenge_produit_flux_list", options={"expose"=true})
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getFlux(Request $request)
    {
        $produitId = $request->get('id');
        $type = $request->get('type');
        $entityName = $type == 'e' ? 'AppBundle:BonEntree' : 'AppBundle:BonSortie';

        $flux = $this->get('challenge.common.service')->getDataToArray($entityName, array('produit = :produit'), array('produit' => $produitId));

        $response = new JsonResponse();

        $response->setData(
            array(
                'recordsTotal'    => count($flux),
                'recordsFiltered' => count($flux),
                'data'            => $flux,
            )
        );

        return $response;
    }

    /**
     * @Route("/flux/update/{type}/{id}", name="_challenge_update_flux", options={"expose"=true})
     * @Route("/flux/add/{type}/{produitId}", name="_challenge_add_flux", options={"expose"=true})
     *
     * @param Request $request
     * @param string  $type
     * @param null    $id
     *
     * @return Response
     *
     * @throws EntityNotFoundException
     */
    public function addOrUpdateFlux(Request $request, $type, $id = null, $produitId = null)
    {
        $em = $this->getDoctrine()->getManager();
        if ($type == 'e') {
            $flux = is_null($id) ? new BonEntree() : $em->getRepository('AppBundle:BonEntree')->find($id);
            if (!$flux instanceof BonEntree) {
                throw new EntityNotFoundException();
            }
        } else {
            $flux = is_null($id) ? new BonSortie() : $em->getRepository('AppBundle:BonSortie')->find($id);
            if (!$flux instanceof BonSortie) {
                throw new EntityNotFoundException();
            }
        }

        if ($request->get('_route') != '_challenge_update_flux') {
            $produit = $em->getRepository('AppBundle:Produit')->find($produitId);
            if (!$produit instanceof Produit) {
                throw new EntityNotFoundException();
            }
        } else {
            $produit = $flux->getProduit();
        }

        $form = $this->createForm(FluxType::class, $flux);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $flux->setProduit($produit);
            
            $update = $request->get('_route') == '_challenge_update_flux';
            try {
                $this->get('challenge.common.service')->saveFlux($produit, $flux, $update);
                $this->addFlash('success', 'Ok');

                return $this->redirect($this->generateUrl('_challenge_produit_flux', array(
                    'id' => $produitId,
                    'type' => $type,
                )));
            } catch (\Exception $exc) {
                $this->addFlash('error', $exc->getMessage());
            }
        }

        return $this->render('AppBundle:Flux:create.html.twig', array(
            'produit' => $produit,
            'type' => $type,
            'form' => $form->createView(),
        ));
    }
}

