<?php

namespace App\Entity;

use App\Repository\SubscriberRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Swagger\Annotations as SWG;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Table(name="subscriber")
 * @ORM\Entity(repositoryClass=SubscriberRepository::class)
 */
class Subscriber extends BaseEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     * @JMS\Groups({"Subscriber:list"})
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     * @JMS\Groups({"Subscriber:list"})
     */
    private $email;

    /**
     * @var string
     * @ORM\Column(name="phone", type="string", length=255, nullable=true)
     * @JMS\Groups({"Subscriber:list"})
     */
    private $phone;

    /**
     * @var string
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     * @JMS\Groups({"Subscriber:list"})
     */
    private $name;

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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

}
