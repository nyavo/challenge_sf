<?php
/**
 * Created by PhpStorm.
 * User: nyavo
 * Date: 17/04/18
 * Time: 23:18
 */
namespace AppBundle\Controller;

use AppBundle\Entity\Fournisseur;
use AppBundle\Form\Type\FournisseurType;
use AppBundle\Form\Type\ProduitType;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class FournisseurController
 *
 * @Route("/fournisseur")
 */
class FournisseurController extends Controller
{
    /**
     * @Route("/list", name="_challenge_fournisseur_list")
     *
     * @return Response
     */
    public function liste()
    {
        return $this->render('AppBundle:Fournisseur:list.html.twig');
    }

    /**
     * @Route("/add", name="_challenge_frs_add", options={"expose"=true})
     * @Route("/update/{id}", name="_challenge_frs_update", options={"expose"=true})
     *
     * @param Request $request
     *
     * @param null    $id
     *
     * @return Response
     *
     * @throws EntityNotFoundException
     */
    public function addOrUpdateFrs(Request $request, $id = null)
    {
        $em = $this->getDoctrine()->getManager();
        $frs = is_null($id) ? new Fournisseur() : $em->getRepository('AppBundle:Fournisseur')->find($id);

        if (!$frs instanceof Fournisseur) {
            throw new EntityNotFoundException();
        }
        $form = $this->createForm(FournisseurType::class, $frs);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em->persist($frs);
                $em->flush();
                $message = $request->get('_route') == '_challenge_frs_add' ? 'Ajout réussi' : 'Modification réussi';
                $this->addFlash('success', $message);
            } catch (\Exception $exc) {
                $this->addFlash('error', $exc->getMessage());
            }

            return $this->redirect($this->generateUrl('_challenge_fournisseur_list'));
        }

        return $this->render('AppBundle:Fournisseur:create.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/get", name="_challenge_get_frs", options={"expose"=true})
     */
    public function getFrs()
    {
        $data = $this->get('challenge.common.service')->getDataToArray('AppBundle:Fournisseur');

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
     * @Route("/remove/{id}", name="_challenge_remove_frs", options={"expose"=true})
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    public function remove($id)
    {
        $em = $this->getDoctrine()->getManager();

        $frs = $em->getRepository('AppBundle:Fournisseur')->find($id);
        $dataReturn = array(
            'success' => false,
            'message' => '',
        );
        if ($frs instanceof Fournisseur) {
            try {
                $em->remove($frs);
                $em->flush();
                $dataReturn['success'] = true;
                $dataReturn['message'] = 'Suppression réussie';
            } catch (\Exception $exc) {
                $dataReturn['message'] = $exc->getMessage();
            }
        } else {
            $dataReturn['message'] = 'Ce fournisseur a déjà été supprimé';
        }
        $response = new JsonResponse();
        $response->setData($dataReturn);

        return $response;
    }
}

