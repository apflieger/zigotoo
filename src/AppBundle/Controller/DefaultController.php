<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Service\HistoryException;
use AppBundle\Service\PageEleveurService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

/**
 * @Route(service="zigotoo.default_controller")
 */
class DefaultController
{
    /**
     * @var TokenStorage
     */
    private $tokenStorage;
    /**
     * @var TwigEngine
     */
    private $templating;
    /**
     * @var FormFactory
     */
    private $formFactory;
    /**
     * @var Router
     */
    private $router;
    /**
     * @var PageEleveurService
     */
    private $pageEleveurService;

    public function __construct(
        TokenStorage $tokenStorage,
        TwigEngine $templating,
        FormFactory $formFactory,
        Router $router,
        PageEleveurService $pageEleveurService
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->templating = $templating;
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->pageEleveurService = $pageEleveurService;
    }

    /**
     * @Route("/", name="teaser")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Exception
     */
    public function teaserAction(Request $request)
    {
        return $this->templating->renderResponse('teaser.html.twig');
    }

    /**
     * @Route("/home", name="index")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Exception
     */
    public function indexAction(Request $request)
    {
        /** @var AnonymousToken $token */
        $token = $this->tokenStorage->getToken();

        /** @var User $user */
        $user = $token->getUser();

        if ($user == 'anon.')
            return $this->templating->renderResponse('index.html.twig');

        $pageEleveur = $this->pageEleveurService->findByOwner($user);

        $form = $this->formFactory->createNamedBuilder('creation-page-eleveur')
            ->add('nom', 'text')
            ->add('save', 'submit', array('label' => 'Créer ma page éleveur'))
            ->getForm();

        $form->handleRequest($request);

        if (!$form->isSubmitted() && $pageEleveur){
            // home d'un eleveur ayant une page eleveur
            return $this->templating->renderResponse('index-eleveur.html.twig', [
                'username' => $user->getUserName(),
                'pageEleveur' => $pageEleveur
            ]);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            // traitement du formulaire de creation de page eleveur
            $nom = $form->getData()['nom'];
            try {
                $slug = $this->pageEleveurService->create($nom, $user)->getSlug();
                return new RedirectResponse($this->router->generate('getPageEleveur', ['pageEleveurSlug' => $slug]));
            } catch (HistoryException $e) {
                switch ($e->getCode()) {
                    case HistoryException::NOM_INVALIDE:
                        return new Response('Le nom "'.$nom.'" n\'est pas valide.', Response::HTTP_NOT_ACCEPTABLE);
                    case HistoryException::SLUG_DEJA_EXISTANT:
                        return new Response('Une page éleveur du même nom existe déjà.', Response::HTTP_CONFLICT);
                    case HistoryException::DEJA_OWNER:
                        return new Response('Vous avez déjà une page éleveur.', Response::HTTP_BAD_REQUEST);
                }
            }
        }

        // home d'un user connecté mais qui n'a pas de page eleveur
        return $this->templating->renderResponse('index-new-eleveur.html.twig', [
            'username' => $user->getUserName(),
            'creationPageEleveur' => $form->createView()
        ]);
    }
}
