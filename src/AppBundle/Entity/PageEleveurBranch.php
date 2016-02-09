<?php
/**
 * Created by PhpStorm.
 * User: arnaudpflieger
 * Date: 19/01/2016
 * Time: 23:25
 */

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PageEleveurBranchRepository")
 * @ORM\Table(name="page_eleveur")
 */
class PageEleveurBranch
{
    /**
     * @ORM\Id
     * @ORM\Column(type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="PageEleveurCommit")
     *
     * @var PageEleveurCommit
     */
    private $commit;

    /**
     *
     * @ORM\OneToOne(targetEntity="User")
     *
     * @var User
     */
    private $owner;

    /**
     * @ORM\Column(type="string", length=120, unique=true)
     *
     * @var string
     */
    private $slug;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param User $owner
     */
    public function setOwner(User $owner)
    {
        $this->owner = $owner;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * @return PageEleveurCommit
     */
    public function getCommit()
    {
        return $this->commit;
    }

    /**
     * @param PageEleveurCommit $commit
     */
    public function setCommit(PageEleveurCommit $commit)
    {
        $this->commit = $commit;
    }
}