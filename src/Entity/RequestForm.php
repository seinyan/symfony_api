<?php

namespace App\Entity;

use App\Repository\RequestFormRepository;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Self_;
use Symfony\Component\Validator\Constraints as Assert;
use Swagger\Annotations as SWG;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Table(name="request_form")
 * @ORM\Entity(repositoryClass=RequestFormRepository::class)
 */
class RequestForm extends BaseEntity
{
    const STATUS_NEW     = 'NEW';
    const STATUS_SUCCESS = 'SUCCESS';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @JMS\Groups({"RequestForm:list", "RequestForm:get"})
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="status", type="string", length=255, nullable=true)
     * @Assert\Length( min = 3, max = 250, groups={"post"})
     * @JMS\Groups({"RequestForm:list", "RequestForm:get", "RequestForm:post"})
     */
    private $status=self::STATUS_NEW;

    /**
     * @var string
     * @ORM\Column(name="fio", type="string", length=255, nullable=true)
     * @Assert\Length( min = 3, max = 250, groups={"post"})
     * @JMS\Groups({"RequestForm:list", "RequestForm:get", "RequestForm:post"})
     */
    private $fio;

    /**
     * @var string
     * @ORM\Column(name="phone", type="string", length=255, nullable=true)
     * @Assert\Length( min = 3, max = 250, groups={"post"})
     * @JMS\Groups({"RequestForm:list", "RequestForm:get", "RequestForm:post"})
     */
    private $phone;

    /**
     * @var string
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     * @Assert\Length( min = 3, max = 250, groups={"post"})
     * @JMS\Groups({"RequestForm:list", "RequestForm:get", "RequestForm:post"})
     */
    private $email;

    /**
     * @var string
     * @ORM\Column(name="message", type="string", length=1000, nullable=true)
     * @Assert\Length( min = 3, max = 250, groups={"post"})
     * @JMS\Groups({"RequestForm:list", "RequestForm:get", "RequestForm:post"})
     */
    private $message;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getFio(): ?string
    {
        return $this->fio;
    }

    public function setFio(?string $fio): self
    {
        $this->fio = $fio;

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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;

        return $this;
    }
}
