<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Swagger\Annotations as SWG;
use JMS\Serializer\Annotation as JMS;

/**
 * Class BaseEntity
 * @package App\Entity
 */
class BaseEntity
{
    /**
     * @var \DateTime
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     * @JMS\Groups({"get", "list"})
     * @JMS\Type("DateTime<'d-m-Y H:i'>")
     * @SWG\Property(example="2018-04-16 08:45")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     * @JMS\Groups({"get", "list"})
     * @JMS\Type("DateTime<'d-m-Y H:i'>")
     * @SWG\Property(example="2018-04-16 08:45")
     */
    protected $updatedAt;



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
