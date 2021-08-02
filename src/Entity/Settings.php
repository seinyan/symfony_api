<?php

namespace App\Entity;

use App\Repository\SettingsRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Swagger\Annotations as SWG;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Table(name="settings")
 * @ORM\Entity(repositoryClass=SettingsRepository::class)
 */
class Settings extends BaseEntityMeta
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     * @JMS\Groups({"Settings:get"})
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", name="email", length=180, nullable=true)
     * @Assert\Email(groups={"Settings:post"})
     * @Assert\NotBlank(groups={"Settings:post"})
     * @SWG\Property(type="string", minLength=4, maxLength=180)
     * @JMS\Groups({"Settings:get", "Settings:post"})
     */
    private $email;

    /**
     * @var string
     * @ORM\Column(name="phone", type="string", length=20, nullable=true)
     * @JMS\Groups({"Settings:get", "Settings:post"})
     */
    private $phone;

    /**
     * @var string
     * @ORM\Column(name="address", type="string", length=1000, nullable=true)
     * @JMS\Groups({"Settings:get", "Settings:post"})
     */
    private $address;

    /**
     * @var string
     * @ORM\Column(name="footer_text", type="string", length=256, nullable=true)
     * @JMS\Groups({"Settings:get", "Settings:post"})
     */
    private $footerText;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getFooterText(): ?string
    {
        return $this->footerText;
    }

    public function setFooterText(?string $footerText): self
    {
        $this->footerText = $footerText;

        return $this;
    }

}
