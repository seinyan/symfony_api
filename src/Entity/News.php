<?php

namespace App\Entity;

use App\Repository\NewsRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Swagger\Annotations as SWG;
use JMS\Serializer\Annotation as JMS;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table(name="news")
 * @ORM\Entity(repositoryClass=NewsRepository::class)
 * @UniqueEntity(fields={"slug"}, groups={"News:post"}, message="slug_account_exists")
 */
class News extends BaseEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     * @JMS\Groups({"News:list", "News:get"})
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", name="slug", length=500, unique=true, nullable=true)
     * @JMS\Groups({"News:list", "News:get", "News:post"})
     */
    private $slug;

    /**
     * @ORM\Column(name="is_publish", type="boolean", nullable=true)
     * @SWG\Property(type="boolean")
     * @JMS\Groups({"News:list", "News:get", "News:post"})
     */
    protected $isPublish=true;

    /**
     * @var \DateTime
     * @ORM\Column(name="publish_at", type="datetime", nullable=true)
     * @JMS\Type("DateTime<'d-m-Y H:i'>")
     * @SWG\Property(example="2018-04-16 08:45")
     * @JMS\Groups({"News:list", "News:get", "News:post"})
     */
    protected $publishAt;

    /**
     * @var string
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     * @Assert\Length( min = 3, max = 250, groups={"post"})
     * @JMS\Groups({"News:list", "News:get", "News:post"})
     */
    protected $title;

    /**
     * @var string
     * @ORM\Column(name="description", type="text", length=65000, nullable=true)
     * @Assert\Length( min = 3, max = 65000, groups={"post"})
     * @JMS\Groups({"News:list", "News:get", "News:post"})
     */
    protected $description;

    /**
     * @ORM\OneToOne(targetEntity="File")
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id", onDelete="SET NULL")
     * @JMS\Groups({"News:get", "News:list"})
     */
    private $image;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
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

    public function getPublishAt(): ?\DateTimeInterface
    {
        return $this->publishAt;
    }

    public function setPublishAt(?\DateTimeInterface $publishAt): self
    {
        $this->publishAt = $publishAt;

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

    public function getImage(): ?File
    {
        return $this->image;
    }

    public function setImage(?File $image): self
    {
        $this->image = $image;

        return $this;
    }
    
}
