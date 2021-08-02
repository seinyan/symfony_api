<?php

namespace App\Entity;

use App\Repository\UserNotificationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Swagger\Annotations as SWG;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Table(name="user_notifications")
 * @ORM\Entity(repositoryClass=UserNotificationRepository::class)
 */
class UserNotification extends BaseEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     * @JMS\Groups({"Notification:list", "Notification:get"})
     */
    private $id;

    /**
     * @ORM\Column(name="is_read", type="boolean", nullable=true, options={"default": false})
     * @SWG\Property(type="boolean")
     * @JMS\Groups({"Order:list", "Order:get", "Order:post"})
     */
    private $isRead=false;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * @JMS\Exclude()
     */
    private $user;

    /**
     * @var string
     * @ORM\Column(name="text", type="text", length=1000, nullable=true)
     * @JMS\Groups({"Notification:list", "Notification:get", "Notification:post"})
     */
    private $text;

    /**
     * @var string
     * @ORM\Column(name="path", type="text", length=1000, nullable=true)
     * @JMS\Groups({"Notification:list", "Notification:get", "Notification:post"})
     */
    private $path;

    /**
     * @ORM\Column(name="is_send_email", type="boolean", nullable=true, options={"default": false})
     * @SWG\Property(type="boolean")
     * @JMS\Groups({"Order:list", "Order:get", "Order:post"})
     */
    private $isSendEmail=false;

    /**
     * @ORM\Column(name="send_email", type="boolean", nullable=true, options={"default": false})
     * @SWG\Property(type="boolean")
     * @JMS\Groups({"Order:list", "Order:get", "Order:post"})
     */
    private $sendEmail=false;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(?string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getIsSendEmail(): ?bool
    {
        return $this->isSendEmail;
    }

    public function setIsSendEmail(?bool $isSendEmail): self
    {
        $this->isSendEmail = $isSendEmail;

        return $this;
    }

    public function getSendEmail(): ?bool
    {
        return $this->sendEmail;
    }

    public function setSendEmail(?bool $sendEmail): self
    {
        $this->sendEmail = $sendEmail;

        return $this;
    }

    public function getIsRead(): ?bool
    {
        return $this->isRead;
    }

    public function setIsRead(?bool $isRead): self
    {
        $this->isRead = $isRead;

        return $this;
    }

}
