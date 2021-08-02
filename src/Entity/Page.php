<?php

namespace App\Entity;

use App\Repository\PageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Swagger\Annotations as SWG;
use JMS\Serializer\Annotation as JMS;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table(name="page")
 * @ORM\Entity(repositoryClass=PageRepository::class)
 * @UniqueEntity(fields={"slug"}, groups={"News:post"}, message="slug_account_exists")
 */
class Page
{
    const TYPE_PAGE     = 'TYPE_PAGE';
    const TYPE_CONTACT  = 'TYPE_CONTACT';
    const TYPE_ABOUT_US = 'TYPE_ABOUT_US';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     * @JMS\Groups({"Page:min", "Page:list", "Page:get"})
     */
    private $id;

    /**
     * @ORM\Column(name="is_publish", type="boolean", nullable=true)
     * @SWG\Property(type="boolean")
     * @JMS\Groups({"Page:list", "Page:get", "Page:post"})
     */
    protected $isPublish=true;

    /**
     * @ORM\Column(name="is_system", type="boolean", nullable=true)
     * @SWG\Property(type="boolean")
     * @JMS\Groups({"Page:list", "Page:get", "Page:post"})
     */
    protected $isSystem=false;

    /**
     * @var string
     * @ORM\Column(name="type", type="string", length=255, nullable=true)
     * @Assert\Length( min = 3, max = 250, groups={"post"})
     * @JMS\Groups({"Page:min", "Page:list", "Page:get", "Page:post"})
     */
    private $type=self::TYPE_PAGE;

    /**
     * @var string
     * @ORM\Column(type="string", name="slug", length=500, unique=true)
     * @Assert\NotBlank(groups={"News:post"})
     * @JMS\Groups({"Page:min", "Page:Page", "Page:get", "Page:post"})
     */
    private $slug;

    /**
     * @var string
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     * @Assert\Length( min = 3, max = 250, groups={"post"})
     * @JMS\Groups({"Page:min", "Page:list", "Page:get", "Page:post"})
     */
    private $title;

    /**
     * @var string
     * @ORM\Column(name="description", type="text", length=65000, nullable=true)
     * @Assert\Length( min = 3, max = 65000, groups={"post"})
     * @JMS\Groups({"Page:list", "Page:get", "Page:post"})
     */
    private $description;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getIsPublish(): ?bool
    {
        return $this->isPublish;
    }

    public function setIsPublish(?bool $isPublish): self
    {
        $this->isPublish = $isPublish;

        return $this;
    }

    public function getIsSystem(): ?bool
    {
        return $this->isSystem;
    }

    public function setIsSystem(?bool $isSystem): self
    {
        $this->isSystem = $isSystem;

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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }



}
