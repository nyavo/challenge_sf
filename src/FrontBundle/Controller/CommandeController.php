<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 19/04/18
 * Time: 23:19
 */
namespace FrontBundle\Controller;

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
     * @Route("/detail/{id}")
     *
     * @param Request $request
     * @param int     $id
     */
    public function detailCommande(Request $request, $id)
    {

    }
}