<?php

namespace App\Entity;

use App\Repository\UserTokenRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Swagger\Annotations as SWG;
use JMS\Serializer\Annotation as JMS;


/**
 * @ORM\Table(name="user_token")
 * @ORM\Entity(repositoryClass=UserTokenRepository::class)
 */
class UserToken
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     * @JMS\Groups({"UserToken:get"})
     */
    private $id;

    /**
     * @ORM\Column(name="is_active", type="boolean", nullable=true, options={"default" : true})
     * @SWG\Property(type="boolean")
     * @JMS\Groups({"UserToken:get"})
     */
    private $isActive=true;

    /**
     * @var \DateTime
     * @ORM\Column(name="last_at", type="datetime", nullable=true)
     * @JMS\Type("DateTime<'d-m-Y H:i'>")
     * @SWG\Property(example="2018-04-16 08:45")
     * @JMS\Groups({"UserToken:get"})
     */
    private $lastAt;

    /**
     * @var string
     * @ORM\Column(name="token", type="text", length=10000, nullable=true)
     * @JMS\Exclude()
     */
    private $token;

    /**
     * @ORM\ManyToOne(targetEntity="User", cascade={"all"} )
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * @JMS\Exclude()
     */
    private $user;

    /**
     * @ORM\OneToOne(targetEntity="UserAgentData", cascade={"all"} )
     * @ORM\JoinColumn(name="user_agent_id", referencedColumnName="id")
     * @JMS\Groups({"UserToken:get"})
     */
    private $userAgent;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getLastAt(): ?\DateTimeInterface
    {
        return $this->lastAt;
    }

    public function setLastAt(?\DateTimeInterface $lastAt): self
    {
        $this->lastAt = $lastAt;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): self
    {
        $this->token = $token;

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

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(?bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }
    
}
