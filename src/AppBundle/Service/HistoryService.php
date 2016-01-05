<?php
/**
 * Created by PhpStorm.
 * User: apf
 * Date: 12/11/15
 * Time: 15:10
 */

namespace AppBundle\Service;


use AppBundle\Entity\BranchInterface;
use AppBundle\Entity\CommitInterface;
use AppBundle\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Exception;


class HistoryService
{
    /** @var ObjectManager */
    private $doctrine;

    /** @var EntityRepository */
    private $branchRepository;

    public function __construct(EntityManager $doctrine,
                                EntityRepository $branchRepository)
    {
        $this->doctrine = $doctrine;
        $this->branchRepository = $branchRepository;
    }

    /**
     * @param BranchInterface $branch
     * @return BranchInterface
     * @throws Exception
     */
    public function create(BranchInterface $branch)
    {
        if (!$branch->getCommit())
            throw new Exception('Commit null');

        if (!$branch->getOwner())
            throw new Exception('Owner null');

        if (empty($branch->getUrl()))
            throw new Exception('Slug null');

        $this->doctrine->persist($branch);
        $this->doctrine->persist($branch->getCommit());

        $this->doctrine->flush();

        return $branch;
    }

    /**
     * @param $branchId int
     * @param CommitInterface $commit
     * @param User $user
     * @throws HistoryException
     */
    public function commit($branchId, CommitInterface $commit, User $user)
    {
        /**
         * @var BranchInterface $pageEleveur
         */
        $pageEleveur = $this->branchRepository->find($branchId);

        if (!$pageEleveur)
            throw new HistoryException(HistoryException::BRANCHE_INCONNUE);

        if ($pageEleveur->getCommit()->getId() !== $commit->getParent()->getId())
            throw new HistoryException(HistoryException::NON_FAST_FORWARD);

        $this->doctrine->persist($commit);
        $pageEleveur->setCommit($commit);

        $this->doctrine->flush();
    }
}