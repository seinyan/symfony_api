<?php

namespace App\Entity;

use App\Repository\FileRepository;
use Doctrine\ORM\Mapping as ORM;
use Swagger\Annotations as SWG;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Table(name="file")
 * @ORM\Entity(repositoryClass=FileRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class File extends BaseEntity
{
    const PRIVATE_BASE_DIR = '/private_files';
    // width
    const IMAGE_SIZE_LARGE  = 'large'; // 1200
    const IMAGE_SIZE_MEDIUM = 'medium'; // 900
    const IMAGE_SIZE_SMALL  = 'small'; // 300

    const IMAGE_SIZES = [
        self::IMAGE_SIZE_LARGE  => [
            'folder' => self::IMAGE_SIZE_LARGE,
            'size' => 1200
        ],
        self::IMAGE_SIZE_MEDIUM => [
            'folder' => self::IMAGE_SIZE_MEDIUM,
            'size' => 900
        ],
        self::IMAGE_SIZE_SMALL  => [
            'folder' => self::IMAGE_SIZE_SMALL,
            'size' => 300
        ],
    ];

    const TYPE_FILE  = 'FILE';
    const TYPE_IMAGE = 'IMAGE';
    const TYPE_AUDIO = 'AUDIO';

    public $subPath;
    public $width;
    public $height;
    public $x;
    public $y;
    public $file;


    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     * @JMS\Groups({"File:get", "File:min"})
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="type", type="string", length=32, nullable=true)
     * @JMS\Groups({"File:list", "File:get", "File:min", "File:post"})
     */
    private $type=self::TYPE_FILE;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * @JMS\Exclude()
     */
    private $user;

    /**
     * @ORM\Column(name="is_private", type="boolean", nullable=true, options={"default" : false})
     * @SWG\Property(type="boolean")
     * @JMS\Groups({"File:list", "File:get", "File:post"})
     */
    private $isPrivate=false;

    /**
     * @var string
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     * @JMS\Groups({"File:list", "File:get", "File:min", "File:post"})
     */
    private $title;

    /**
     * @var string
     * @ORM\Column(name="format", type="string", length=1000, nullable=true)
     * @JMS\Groups({"File:list", "File:get", "File:post"})
     */
    private $format;

    /**
     * @var string
     * @ORM\Column(name="name", type="string", length=1000, nullable=true)
     * @JMS\Groups({"File:list", "File:get", "File:post"})
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(name="path", type="string", length=1000, nullable=true)
     * @JMS\Groups({"File:list", "File:get", "File:post"})
     */
    private $path;



    public function getFullPath()
    {
        return self::PRIVATE_BASE_DIR.$this->getPath().$this->getName();
    }

    /**
     * @var string
     * @JMS\VirtualProperty()
     * @JMS\Type("string")
     * @JMS\SerializedName("domain")
     * @JMS\Groups({"File:get", "File:list", "File:min"})
     * @SWG\Property(description="domain")
     */
    public function domainVp()
    {
        return $_ENV['API_DOMAIN'];
    }

    /**
     * @JMS\VirtualProperty()
     * @JMS\Type("array")
     * @JMS\SerializedName("urls")
     * @JMS\Groups({"File:get", "File:list", "File:min"})
     * @SWG\Property(description="File urls array [] ")
     */
    public function urlsVp()
    {
        if ($this->getIsPrivate()) {
            return [
                'url' => $_ENV['APP_DOMAIN'].'/file/get/'.$this->id
            ];
        }

        $urlArr = [
            'url' => $_ENV['API_DOMAIN'].$this->getPath().$this->getName(),
        ];

        foreach (File::IMAGE_SIZES as $IMAGE_SIZE) {
            $urlArr[$IMAGE_SIZE['folder']] = $this->getPath().$IMAGE_SIZE['folder'].'/'.$this->getName();
        }

        return $urlArr;
    }

    /**
     * @var string
     * @JMS\VirtualProperty()
     * @JMS\Type("string")
     * @JMS\SerializedName("url")
     * @JMS\Groups({"File:get", "File:list", "File:min"})
     * @SWG\Property(description="url")
     */
    public function urlVp()
    {
        return $_ENV['API_DOMAIN'].$this->getPath().$this->getName();
    }

    /** @ORM\PreRemove()  */
    public function removeFile()
    {
        //@unlink($this->path.$this->name);
        shell_exec('rm -rf '.$this->path.$this->name);
    }

    public function getId(): ?string
    {
        return $this->id;
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

    public function getIsPrivate(): ?bool
    {
        return $this->isPrivate;
    }

    public function setIsPrivate(?bool $isPrivate): self
    {
        $this->isPrivate = $isPrivate;

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

    public function getFormat(): ?string
    {
        return $this->format;
    }

    public function setFormat(?string $format): self
    {
        $this->format = $format;

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

    public function getSubPath(): ?string
    {
        return $this->subPath;
    }

    public function setSubPath(?string $subPath): self
    {
        $this->subPath = $subPath;

        return $this;
    }

    public function getWidth(): ?string
    {
        return $this->width;
    }

    public function setWidth(?string $width): self
    {
        $this->width = $width;

        return $this;
    }

    public function getHeight(): ?string
    {
        return $this->height;
    }

    public function setHeight(?string $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function getX(): ?string
    {
        return $this->x;
    }

    public function setX(?string $x): self
    {
        $this->x = $x;

        return $this;
    }

    public function getY(): ?string
    {
        return $this->y;
    }

    public function setY(?string $y): self
    {
        $this->y = $y;

        return $this;
    }

    public function getFile():UploadedFile
    {
        return $this->file;
    }

    public function setFile(UploadedFile $file)
    {
        $this->file = $file;

        return $this;
    }




}