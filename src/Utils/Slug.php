<?php
namespace App\Utils;

use Cocur\Slugify\Slugify;

/**
 * Class Slug
 * @package App\Utils
 */
class Slug
{
    /** @var Slugify */
    private $slugify;

    /**
     * Slug constructor.
     */
    public function __construct()
    {
        $this->slugify = new Slugify([
            'separator' => '_',
            'lowercase' => true, //$lowercase
        ]);
    }

    /**
     * @param $str
     * @return string
     */
    public function textToSlug($str)
    {
        return $this->slugify->slugify($str);
    }
}