<?php

namespace App\Services;

use App\AppConsts;
use App\Entity\Page;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Intl\Timezones;

/**
 * Class InitService
 * @package App\Services
 */
class InitService
{
    /** @var EntityManager */
    private $em;

    /**
     * InitService constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    public function init()
    {
        $em = $this->em;

        $arr = [
            [
                'title' => 'Контакты',
                'slug' => 'contact',
                'type' => Page::TYPE_CONTACT,
            ],
            [
                'title' => 'О нас',
                'slug' => 'o_nas',
                'type' => Page::TYPE_ABOUT_US,
            ],
        ];

        foreach ($arr as $value) {
            $item = new Page();
            $item->setIsPublish(true);
            $item->setIsSystem(true);

            $item->setType($value['type']);
            $item->setSlug($value['slug']);
            $item->setTitle($value['title']);

            $em->persist($item);
            dump($item);
        }

        $em->flush();
    }

    public function syncAppTimezones()
    {
        \Locale::setDefault('en');
        $timezones = Timezones::getNames();
        foreach ($timezones as $k=>$str) {
            $obj = new AppTimezones();
            $obj->setLabel($k.' '.Timezones::getGmtOffset($k));
            $obj->setValue(Timezones::getGmtOffset($k));
            $obj->setTz($k);
            $this->em->persist($obj);
        }

        $this->em->flush();
    }


    /**
     * @param $model
     * @return \Doctrine\Common\Persistence\ObjectRepository|\Doctrine\ORM\EntityRepository
     */
    public function Repository($model)
    {
        return $this->em->getRepository($model);
    }
}