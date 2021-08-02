<?php

namespace App\Entity;

use App\Repository\SliderItemRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Swagger\Annotations as SWG;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Table(name="slider_item")
 * @ORM\Entity(repositoryClass=SliderItemRepository::class)
 */
class SliderItem
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     * @JMS\Groups({"SliderItem:list", "SliderItem:get"})
     */
    private $id;

    /**
     * @ORM\Column(name="is_publish", type="boolean", nullable=true)
     * @SWG\Property(type="boolean")
     * @JMS\Groups({"SliderItem:list", "SliderItem:get", "SliderItem:post"})
     */
    private $isPublish=true;

    /**
     * @var string
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     * @Assert\Length( min = 3, max = 250, groups={"post"})
     * @JMS\Groups({"SliderItem:list", "SliderItem:get", "SliderItem:post"})
     */
    private $title;

    /**
     * @var string
     * @ORM\Column(name="link", type="text", length=1000, nullable=true)
     * @Assert\Length( min = 3, max = 250, groups={"post"})
     * @JMS\Groups({"SliderItem:list", "SliderItem:get", "SliderItem:post"})
     */
    private $link;

    /**
     * @ORM\OneToOne(targetEntity="File")
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id", onDelete="SET NULL")
     * @JMS\Groups({"SliderItem:list", "SliderItem:get", "SliderItem:post"})
     */
    private $image;

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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): self
    {
        $this->link = $link;

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
