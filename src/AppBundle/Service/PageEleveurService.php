<?php
/**
 * Created by PhpStorm.
 * User: arnaudpflieger
 * Date: 04/01/2016
 * Time: 23:24
 */

namespace AppBundle\Service;


use AppBundle\Entity\ERole;
use AppBundle\Entity\PageEleveur;
use AppBundle\Entity\PageEleveurCommit;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

class PageEleveurService
{
    /**
     * @var HistoryService
     */
    private $history;
    /**
     * @var EntityRepository
     */
    private $pageEleveurRepository;

    public function __construct(HistoryService $history, EntityRepository $pageEleveurRepository)
    {
        $this->history = $history;
        $this->pageEleveurRepository = $pageEleveurRepository;
    }

    public function create(string $nom, User $owner)
    {
        if (count($this->pageEleveurRepository->findBy(['owner' => $owner])) > 0)
            throw new PageEleveurException('user ' . $owner->getId() . ' a deja une page eleveur');


        $commit = new PageEleveurCommit($nom, '', null);
        $pageEleveur = new PageEleveur();
        $pageEleveur->setCommit($commit);
        $pageEleveur->setOwner($owner);
        $pageEleveur->setUrl(self::slug($nom));

        if (empty($pageEleveur->getUrl()))
            throw new PageEleveurException('Le nom n\'"'.$nom.'"est pas valide');

        if (count($this->pageEleveurRepository->findBy(['url' => $pageEleveur->getUrl()])) > 0)
            throw new PageEleveurException('Une page eleveur du meme nom existe deja');

        try {
            return $this->history->create($pageEleveur);
        } finally {
            $owner->addRole(ERole::ELEVEUR);
        }
    }

    /**
     * @param $str string
     * @return string
     */
    public static function slug($str)
    {
        // conversion de tous les caractères spéciaux vers de l'ascii
        $ascii = iconv('UTF-8', 'ASCII//TRANSLIT', $str);

        // suppression de tous les caractères qui ne sont pas des chiffres, lettres, ou "_+- "
        $ascii = preg_replace('/[^a-zA-Z0-9\/_+ -]/', '', $ascii);

        // lowercase
        $ascii = strtolower($ascii);

        // remplacement de tout ce qui n'est pas chiffres ou lettres par le séparateur '-'
        $ascii = preg_replace('/[\/_+ -]+/', '-', $ascii);

        // trim
        return trim($ascii, '-');
    }
}