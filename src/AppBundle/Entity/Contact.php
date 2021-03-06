<?php


namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints;

/**
 * @ORM\Entity
 * @ORM\Table(name="contact")
 */
class Contact implements IdentityPersistableInterface
{
    use Persistable;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     *
     * @Constraints\NotBlank()
     * @Constraints\Length(max = 255)
     */
    private $email;

    /**
     * @var string
     * @ORM\Column(type="string", length=1000)
     *
     * @Constraints\NotBlank()
     * @Constraints\Length(max = 1000)
     */
    private $message;

    /**
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     *
     * @var User
     */
    private $user;

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }
}