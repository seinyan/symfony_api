<?php

namespace App\Entity;

use App\Repository\UserPersonRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Swagger\Annotations as SWG;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Table(name="user_person")
 * @ORM\Entity(repositoryClass=UserPersonRepository::class)
 */
class UserPerson
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="bigint", name="id")
     * @JMS\Groups({"User:get", "User:list"})
     * @SWG\Property(description="ID")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="File")
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id")
     * @JMS\Groups({"User:get", "User:list", "User:min"})
     */
    private $image;

    /**
     * @JMS\VirtualProperty()
     * @JMS\Type("string")
     * @JMS\SerializedName("image")
     * @JMS\Groups({"User:post"})
     * @SWG\Property(description="image id")
     */
    public function imageVp(){}

    /**
     * имя
     * @var string
     * @ORM\Column(name="first_name", type="string", length=128, nullable=true)
     * @JMS\Groups({"User:get", "User:list", "User:post"})
     */
    private $firstName;

    /**
     * фамилия
     * @var string
     * @ORM\Column(name="last_name", type="string", length=128, nullable=true)
     * @JMS\Groups({"User:get", "User:list", "User:post"})
     */
    private $lastName;

    /**
     * фамилия
     * @var string
     * @ORM\Column(name="middle_name", type="string", length=128, nullable=true)
     * @JMS\Groups({"User:get", "User:list", "User:post"})
     */
    private $middleName;

    /**
     * @JMS\VirtualProperty()
     * @JMS\Type("string")
     * @JMS\SerializedName("full_name")
     * @JMS\Groups({"User:get", "User:list", "User:min"}))
     * @SWG\Property(description="full_name")
     */
    public function fullNameVp()
    {
        if ($this->firstName || $this->lastName) {
            return $this->firstName.' '.$this->lastName;
        }

        return " ";
    }

    /**
     * @ORM\OneToOne(targetEntity="User", mappedBy="person")
     * @JMS\Exclude()
     */
    private $user;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getMiddleName(): ?string
    {
        return $this->middleName;
    }

    public function setMiddleName(?string $middleName): self
    {
        $this->middleName = $middleName;

        return $this;
    }

    public function getImage(): ?File
    {
        return $this->image;
    }

    public function setImage(?File $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        // unset the owning side of the relation if necessary
        if ($user === null && $this->user !== null) {
            $this->user->setPerson(null);
        }

        // set the owning side of the relation if necessary
        if ($user !== null && $user->getPerson() !== $this) {
            $user->setPerson($this);
        }

        $this->user = $user;

        return $this;
    }
    
}
