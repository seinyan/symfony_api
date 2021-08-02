<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Swagger\Annotations as SWG;
use JMS\Serializer\Annotation as JMS;

class BaseEntityMeta
{
    /**
     * @var string
     * @ORM\Column(name="meta_title", type="string", length=255, nullable=true)
     * @Assert\Length( min = 3, max = 250 )
     * @JMS\Groups({"list", "get", "post"})
     */
    protected $metaTitle;

    /**
     * @var string
     * @ORM\Column(name="meta_keywords", type="string", length=500, nullable=true)
     * @Assert\Length( min = 3, max = 500)
     * @JMS\Groups({"list", "get", "post"})
     */
    protected $metaKeywords;

    /**
     * @var string
     * @ORM\Column(name="meta_description", type="string", length=500, nullable=true)
     * @Assert\Length( min = 3, max = 500)
     * @JMS\Groups({"list", "get", "post"})
     */
    protected $metaDescription;

    /**
     * @var \DateTime
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     * @JMS\Type("DateTime<'d-m-Y H:i'>")
     * @SWG\Property(example="2018-04-16 08:45")
     * @JMS\Groups({"list", "get"})
     */
    protected $createdAt;

    /**
     * @var \DateTime
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     * @JMS\Type("DateTime<'d-m-Y H:i'>")
     * @SWG\Property(example="2018-04-16 08:45")
     * @JMS\Groups({"list", "get"})
     */
    protected $updatedAt;

    public function getMetaTitle(): ?string
    {
        return $this->metaTitle;
    }

    public function setMetaTitle(?string $metaTitle): self
    {
        $this->metaTitle = $metaTitle;

        return $this;
    }

    public function getMetaKeywords(): ?string
    {
        return $this->metaKeywords;
    }

    public function setMetaKeywords(?string $metaKeywords): self
    {
        $this->metaKeywords = $metaKeywords;

        return $this;
    }

    public function getMetaDescription(): ?string
    {
        return $this->metaDescription;
    }

    public function setMetaDescription(?string $metaDescription): self
    {
        $this->metaDescription = $metaDescription;

        return $this;
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

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
