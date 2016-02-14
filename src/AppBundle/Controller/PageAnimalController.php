<?php
/**
 * Created by PhpStorm.
 * User: arnaudpflieger
 * Date: 13/01/2016
 * Time: 21:49
 */

namespace AppBundle\Controller;


use AppBundle\Service\PageAnimalService;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Route(service="zigotoo.page_animal_controller")
 */
class PageAnimalController
{
    /**
     * @var TwigEngine
     */
    private $templating;
    /**
     * @var PageAnimalService
     */
    private $pageAnimalService;

    public function __construct(
        TwigEngine $templating,
        PageAnimalService $pageAnimalService
    ) {
        $this->templating = $templating;
        $this->pageAnimalService = $pageAnimalService;
    }

    /**
     * @Route("/animal/{pageAnimalId}", name="getPageAnimal")
     * @Method("GET")
     */
    public function getAction($pageAnimalId)
    {

        $pageAnimal = $this->pageAnimalService->find($pageAnimalId);

        if (!$pageAnimal)
            throw new NotFoundHttpException(null, null);

        return $this->templating->renderResponse('base.html.twig', [
            'inject' => 'page-animal',
            'pageAnimal' => $pageAnimal
        ]);
    }
}