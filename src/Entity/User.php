<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Swagger\Annotations as SWG;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"email"}, groups={"User:register"}, message="_account_exists")
 * @UniqueEntity(fields={"phone"}, groups={"User:register"}, message="_account_exists")
 */
class User implements UserInterface
{
    const ROLE_USER    = 'ROLE_USER';
    const ROLE_ADMIN   = 'ROLE_ADMIN';
    const ROLE_MANAGER = 'ROLE_MANAGER';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="bigint", name="id")
     * @SWG\Property(description="ID")
     * @JMS\Groups({"User:get", "User:list", "User:min"})
     */
    private $id;

    /**
     * @ORM\Column(name="is_active", type="boolean", nullable=true)
     * @SWG\Property(type="boolean")
     * @JMS\Groups({"User:get", "User:list", "User:min"})
     */
    private $isActive=true;

    /**
     * @var \DateTime
     * @ORM\Column(name="registered_at", type="datetime", nullable=true)
     * @JMS\Type("DateTime<'d-m-Y H:i'>")
     * @SWG\Property(example="2018-04-16 08:45")
     * @JMS\Groups({"User:get", "User:list", "User:min"})
     */
    private $registeredAt;

    /**
     * @var \DateTime
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     * @JMS\Type("DateTime<'d-m-Y H:i'>")
     * @SWG\Property(example="2018-04-16 08:45")
     * @JMS\Groups({"User:get", "User:list", "User:min"})
     */
    private $updatedAt;

    /**
     * E-mail
     * @var string
     * @ORM\Column(type="string", name="email", length=180, unique=true)
     * @Assert\Email(groups={"User:register"})
     * @Assert\NotBlank(groups={"User:register"})
     * @SWG\Property(type="string", minLength=4, maxLength=180)
     * @JMS\Groups({"User:get", "User:list", "User:min"})
     */
    private $email;

    /**
     * Phone
     * @var string
     * @ORM\Column(name="phone", type="string", length=20, nullable=true, unique=true)
     * @Assert\Regex(pattern="/^[0-9]*$/", message="_phone_not_valid", groups={"User:register"})
     * @JMS\Groups({"User:get", "User:list", "User:min"})
     */
    private $phone;

    /**
     * The salt to use for hashing.
     * @var string
     * @ORM\Column(name="salt", type="string", length=255, nullable=true)
     */
    protected $salt;

    private $roles = [];

    /**
     * @var string
     * @ORM\Column(name="role", type="string", length=30, nullable=true)
     * @SWG\Property(type="string", example="ROLE_ADMIN || ROLE_USER")
     * @JMS\Groups({"User:get", "User:list", "User:min"})
     */
    private $role;

    /**
     * @var string The hashed password
     * @ORM\Column(type="string", name="password", nullable=true)
     * @JMS\Groups({"User:register"})
     */
    private $password;

    /**
     * @ORM\OneToOne(targetEntity="UserPerson", inversedBy="user", cascade={"persist"} )
     * @ORM\JoinColumn(name="person_id", referencedColumnName="id")
     * @JMS\Groups({"User:get", "User:list", "User:min"})
     */
    private $person;

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = $this->role;

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getSalt(): ?string
    {
        return $this->salt;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function getRegisteredAt(): ?\DateTimeInterface
    {
        return $this->registeredAt;
    }

    public function setRegisteredAt(?\DateTimeInterface $registeredAt): self
    {
        $this->registeredAt = $registeredAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(?string $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getPerson(): ?UserPerson
    {
        return $this->person;
    }

    public function setPerson(?UserPerson $person): self
    {
        $this->person = $person;

        return $this;
    }

    public function setSalt(?string $salt): self
    {
        $this->salt = $salt;

        return $this;
    }
}
