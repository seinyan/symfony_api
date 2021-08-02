<?php

namespace App\Entity;

use App\Repository\UserLogRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Swagger\Annotations as SWG;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Table(name="user_log")
 * @ORM\Entity(repositoryClass=UserLogRepository::class)
 */
class UserLog
{
    const SECURITY_LOGIN_SUCCESS = 'SECURITY_LOGIN_SUCCESS';
    const SECURITY_LOGIN_FAILURE = 'SECURITY_LOGIN_FAILURE';

    const SECURITY_LOGIN_TOKEN_SUCCESS = 'SECURITY_LOGIN_TOKEN_SUCCESS';
    const SECURITY_LOGIN_TOKEN_FAILURE = 'SECURITY_LOGIN_TOKEN_FAILURE';

    const USER_LOGOUT  = 'USER_LOGOUT';
    const USER_CLEAR_ALL_SESSION = 'USER_CLEAR_ALL_SESSION';

    const USER_UPDATE      = 'USER_UPDATE';
    const USER_UPDATE_PASS = 'USER_UPDATE_PASS';

    const USER_REGISTER = 'USER_REGISTER';
    const USER_RESTORE  = 'USER_RESTORE';



    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     * @JMS\Groups({"UserLog:get"})
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="type", type="string", nullable=true)
     * @JMS\Groups({"UserLog:get"})
     */
    private $type;
    
    /**
     * @var \DateTime
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     * @JMS\Type("DateTime<'d-m-Y H:i'>")
     * @SWG\Property(example="2018-04-16 08:45")
     * @JMS\Groups({"UserLog:get"})
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="User", cascade={"all"} )
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * @JMS\Exclude()
     */
    private $user;

    /**
     * @ORM\OneToOne(targetEntity="UserAgentData", cascade={"all"} )
     * @ORM\JoinColumn(name="user_agent_id", referencedColumnName="id")
     * @JMS\Groups({"UserLog:get"})
     */
    private $userAgent;

    /**
     * @JMS\VirtualProperty()
     * @JMS\Type("string")
     * @JMS\SerializedName("type_title")
     * @SWG\Property(description="type_title")
     * @JMS\Groups({"UserLog:get"})
     */
    public function type_titleVp()
    {
        return $this->type;
    }

    public function __construct($type)
    {
        $this->type = $type;
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getUserAgent(): ?UserAgentData
    {
        return $this->userAgent;
    }

    public function setUserAgent(?UserAgentData $userAgent): self
    {
        $this->userAgent = $userAgent;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

}
