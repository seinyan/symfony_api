<?php

namespace App\Entity;

use App\Repository\UserAgentDataRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Swagger\Annotations as SWG;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Table(name="user_agent_data")
 * @ORM\Entity(repositoryClass=UserAgentDataRepository::class)
 */
class UserAgentData
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     * @JMS\Groups({"UserAgentData:get"})
     */
    private $id;

    /**
     * @var \DateTime
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     * @JMS\Type("DateTime<'d-m-Y H:i'>")
     * @SWG\Property(example="2018-04-16 08:45")
     * @JMS\Groups({"UserAgentData:get"})
     */
    private $createdAt;

    /**
     * @var string
     * @ORM\Column(name="user_agent", type="string", length=10000, nullable=true)
     * @JMS\Groups({"UserAgentData:get"})
     */
    private $userAgent;

    /**
     * @var string
     * @ORM\Column(name="platform", type="string", length=128, nullable=true)
     * @JMS\Groups({"UserAgentData:get"})
     */
    private $platform;

    /**
     * @var string
     * @ORM\Column(name="browser", type="string", length=128, nullable=true)
     * @JMS\Groups({"UserAgentData:get"})
     */
    private $browser;

    /**
     * @var string
     * @ORM\Column(name="device", type="string", length=128, nullable=true)
     * @JMS\Groups({"UserAgentData:get"})
     */
    private $device;

    /**
     * @var string
     * @ORM\Column(name="ip", type="string", length=256, nullable=true)
     * @JMS\Groups({"UserAgentData:get"})
     */
    private $ip;

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

    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }

    public function setUserAgent(?string $userAgent): self
    {
        $this->userAgent = $userAgent;

        return $this;
    }

    public function getPlatform(): ?string
    {
        return $this->platform;
    }

    public function setPlatform(?string $platform): self
    {
        $this->platform = $platform;

        return $this;
    }

    public function getBrowser(): ?string
    {
        return $this->browser;
    }

    public function setBrowser(?string $browser): self
    {
        $this->browser = $browser;

        return $this;
    }

    public function getDevice(): ?string
    {
        return $this->device;
    }

    public function setDevice(?string $device): self
    {
        $this->device = $device;

        return $this;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(?string $ip): self
    {
        $this->ip = $ip;

        return $this;
    }

}
