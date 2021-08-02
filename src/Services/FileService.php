<?php

namespace App\Services;

use App\AppConsts;
use App\Entity\File;
use App\Entity\Image;
use Doctrine\ORM\EntityManager;
use Spatie\ImageOptimizer\OptimizerChainFactory;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use \Gumlet\ImageResize;

use WebPConvert\WebPConvert;
use WebPConvert\Convert\Converters\Gd;
use WebPConvert\Loggers\EchoLogger;
use WebPConvert\Convert\Converters\Stack;


/**
 * Class FileService
 * @package App\Services
 */
class FileService
{
    /** @var EntityManager */
    private $em;

    /** @var Filesystem  */
    private $filesystem;

    /** @var string  */
    private $privateDir;

    /** @var string  */
    private $dir;

    /** @var string  */
    private $project_dir;

    /** @var \stdClass  */
    private $result;

    /**
     * FileService constructor.
     * @param EntityManager $entityManager
     * @param $project_dir
     */
    public function __construct(EntityManager $entityManager, $project_dir)
    {
        $this->em = $entityManager;
        $this->filesystem = new Filesystem();

        $this->project_dir = $project_dir;
        $this->privateDir  = $this->project_dir.File::PRIVATE_BASE_DIR;
        $this->dir         = $this->project_dir.'/public';

        $this->result = new \stdClass();
        $this->result->data = null;
        $this->result->status = null;
    }

    /**
     * @param File $entity
     * @return \stdClass
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function uploadFile(File $entity)
    {
        /** @var UploadedFile $file */
        $file = $entity->file;

        $entity->setFormat($entity->file->guessClientExtension());
        $file = $entity->getFile();
        if (!$entity->getTitle()) {
            $entity->setTitle(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME).'.'.$file->guessClientExtension());
        }

        $entity->setPath('/files/'.$entity->subPath.'/');
        $this->em->persist($entity);
        $this->em->flush();
        $entity->setName($entity->getId().'.'.$entity->getFormat());

        $dir = $this->getDir($entity);
        $this->existsOrCreateDir($dir.$entity->getPath());

        try {
            $file->move($dir.$entity->getPath(), $entity->getName());
            $this->em->flush();

            $this->result->status = AppConsts::CODE_200;
            $this->result->data   = $entity;
        }
        catch(FileException $e) {
            $this->em->remove($entity);
            $this->em->flush();

            $this->result->status = AppConsts::CODE_INVALID_INPUT_400;
            $this->result->data   = 'err'.$e->getMessage();
        }

        return $this->result;
    }

    /**
     * @param File $entity
     * @return \stdClass
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Gumlet\ImageResizeException
     * @throws \WebPConvert\Convert\Exceptions\ConversionFailedException
     */
    public function uploadImage(File $entity)
    {
        /** @var UploadedFile $file */
        $file = $entity->file;

        $entity->setFormat($entity->file->guessClientExtension());
        $file = $entity->getFile();
        if (!$entity->getTitle()) {
            $entity->setTitle(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME).'.'.$file->guessClientExtension());
        }

        $this->em->persist($entity);
        $this->em->flush();
        $entity->setName($entity->getId().'.'.$entity->getFormat());
        $entity->setPath('/images/'.$entity->subPath.'/'.$entity->getId().'/');
        $this->em->flush();

        try {

            $dir = $this->getDir($entity);
            $this->existsOrCreateDir($dir.$entity->getPath());
            $this->createImagesDirs($dir.$entity->getPath());

            $image = new ImageResize($file->getRealPath());
            $image->save($dir.$entity->getPath() . $entity->getName());
            foreach (File::IMAGE_SIZES as $IMAGE_SIZE) {
                $image = new ImageResize($dir.$entity->getPath() . $entity->getName());
                $image->resizeToWidth($IMAGE_SIZE['size']);
                $image->save($dir.$entity->getPath().$IMAGE_SIZE['folder'].'/'. $entity->getName());
            }
            $this->convertCropWebP($entity);


            $this->em->flush();

            $this->result->status = AppConsts::CODE_200;
            $this->result->data   = $entity;
        }
        catch(FileException $e) {
            $this->em->remove($entity);
            $this->em->flush();

            $this->result->status = AppConsts::CODE_INVALID_INPUT_400;
            $this->result->data   = 'err'.$e->getMessage();
        }

        return $this->result;
    }

    /**
     * @param File $entity
     * @return \stdClass
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Gumlet\ImageResizeException
     */
    public function uploadImageCkeditor(File $entity)
    {
        /** @var UploadedFile $file */
        $file = $entity->file;

        $entity->setFormat($entity->file->guessClientExtension());
        $file = $entity->getFile();
        if (!$entity->getTitle()) {
            $entity->setTitle(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME).'.'.$file->guessClientExtension());
        }

        $entity->setPath('/files/'.$entity->subPath.'/');
        $this->em->persist($entity);
        $this->em->flush();
        $entity->setName($entity->getId().'.'.$entity->getFormat());

        $dir = $this->getDir($entity);
        $this->existsOrCreateDir($dir.$entity->getPath());

        try {
            $image = new ImageResize($file->getRealPath());
            $image->resizeToWidth(900);
            $image->save($dir.$entity->getPath() . $entity->getName());

            $this->em->flush();

            $this->result->status = AppConsts::CODE_200;
            $this->result->data   = $entity;
        }
        catch(FileException $e) {
            $this->em->remove($entity);
            $this->em->flush();

            $this->result->status = AppConsts::CODE_INVALID_INPUT_400;
            $this->result->data   = 'err'.$e->getMessage();
        }

        return $this->result;
    }


    /**
     * @param File $entity
     * @return \stdClass
     * @throws \Gumlet\ImageResizeException
     * @throws \WebPConvert\Convert\Exceptions\ConversionFailedException
     */
    public function imageCrop(File $entity)
    {
        $dir = $this->getDir($entity);
        $firePath = $dir.$entity->getPath().$entity->getName();

        $image = new ImageResize($firePath);
        $image->freecrop($entity->width, $entity->height, $entity->x, $entity->y);
        $image->save($dir.$entity->getPath().File::IMAGE_SIZE_LARGE.'/'. $entity->getName());

        $firePath = $dir.$entity->getPath().File::IMAGE_SIZE_LARGE.'/'. $entity->getName();

        foreach (File::IMAGE_SIZES as $IMAGE_SIZE) {
            $image = new ImageResize($firePath);
            $image->resizeToWidth($IMAGE_SIZE['size']);
            $image->save($dir.$entity->getPath().$IMAGE_SIZE['folder'].'/'. $entity->getName());
        }
        $this->convertCropWebP($entity);

        $this->result->status = AppConsts::CODE_200;
        $this->result->data   = $entity;

        return $this->result;
    }


    /**
     * @param File $entity
     * @throws \WebPConvert\Convert\Exceptions\ConversionFailedException
     */
    public function convertCropWebP(File $entity)
    {
        if ($entity->getFormat() === 'webp') {
            return false;
        }

        $dir = $this->getDir($entity);
        $this->convertWebP($dir.$entity->getPath().$entity->getName());
        foreach (File::IMAGE_SIZES as $IMAGE_SIZE) {
            $this->convertWebP($dir.$entity->getPath().$IMAGE_SIZE['folder'].'/'.$entity->getName());
        }
    }

    /**
     * @param $source
     * @throws \WebPConvert\Convert\Exceptions\ConversionFailedException
     */
    public function convertWebP($source)
    {
        $destination = $source.'.webp';
        $options = [
            'converters' => [
                'vips', 'imagick', 'gmagick', 'imagemagick', 'graphicsmagick', 'wpc', 'ewww',
                'gd',  // 'cwebp',
            ],
        ];

        WebPConvert::convert($source, $destination, $options);
    }


    /** @return string */
    public function getDir(File $entity)
    {
        if($entity->getIsPrivate()) {
            return $this->privateDir;
        }
        return $this->dir;
    }

    /**
     * @param $path
     */
    public function createImagesDirs($path)
    {
        $this->existsOrCreateDir($path);
        foreach (File::IMAGE_SIZES as $IMAGE_SIZE) {
            $this->existsOrCreateDir($path.$IMAGE_SIZE['folder']);
        }
    }

    /** @param $path */
    public function existsOrCreateDir($path)
    {
        if(!$this->filesystem->exists($path)) {
            $this->filesystem->mkdir($path, 0777);
        }
    }
}