<?php
/**
 * Created by PhpStorm.
 * User: apf
 * Date: 05/11/15
 * Time: 20:52
 */

namespace AppBundle\Controller;


use AppBundle\Entity\PageEleveurCommit;
use AppBundle\Service\PageEleveurService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class PageEleveurController extends Controller
{
    /**
     * @Route("/{eleveurURL}", name="getPageEleveur")
     * @Method("GET")
     */
    public function getAction($eleveurURL)
    {
        /**
         * @var PageEleveurService $pageEleveurService
         */
        $pageEleveurService = $this->container->get('page_eleveur');

        $pageEleveur = $pageEleveurService->getByUrl($eleveurURL);

        if (!$pageEleveur)
            throw $this->createNotFoundException();

        return $this->render('page-eleveur.html.twig',['pageEleveur' => $pageEleveur]);
    }

    /**
     * @Route("/commit-page-eleveur", name="commitPageEleveur")
     * @Method("POST")
     */
    public function commitAction(Request $request)
    {
        $description = $request->request->get('description');
        $head = $request->request->get('head');
        $pageEleveurId = $request->request->get('pageEleveur');

        /**
         * @var TokenStorage $tokenStorage
         */
        $tokenStorage = $this->container->get('security.token_storage');
        /**
         * @var User $user
         */
        $user = $tokenStorage->getToken()->getUser();

        /**
         * @var PageEleveurService $pageEleveurService
         */
        $pageEleveurService = $this->container->get('page_eleveur');
        $commit = $pageEleveurService->getCommit($head);

        $newCommit = new PageEleveurCommit($commit->getNom(), $description, $commit);

        $pageEleveurService->commit($pageEleveurId, $newCommit, $user);

        return new Response();
    }
}