<?php

namespace NettePalette;

use Nette\Object;

/**
 * Class LatteFilter
 * @package Palette
 */
class LatteFilter extends Object {

    /**
     * @var Palette
     */
    private $palette;


    /**
     * LatteFilter constructor.
     * @param Palette $palette
     */
    public function __construct(Palette $palette) {

        $this->palette = $palette;
    }


    /**
     * @param $image
     * @param null $imageQuery
     * @return null|string
     */
    public function __invoke($image, $imageQuery = NULL) {

        return $this->palette->getUrl($image, $imageQuery);
    }

}