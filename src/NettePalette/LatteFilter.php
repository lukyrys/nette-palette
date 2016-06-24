<?php

/**
 * This file is part of the Nette Palette (https://github.com/MichaelPavlista/nette-palette)
 * Copyright (c) 2016 Michael Pavlista (http://www.pavlista.cz/)
 *
 * @author Michael Pavlista
 * @email  michael@pavlista.cz
 * @link   http://pavlista.cz/
 * @link   https://www.facebook.com/MichaelPavlista
 * @copyright 2016
 */

namespace NettePalette;

/**
 * Providing Palette support to Latte template engine
 * Class LatteFilter
 * @package NettePalette
 */
class LatteFilter
{
    /** @var Palette service */
    private $palette;


    /**
     * LatteFilter constructor.
     * @param Palette $palette
     */
    public function __construct(Palette $palette)
    {
        $this->palette = $palette;
    }


    /**
     * Return url to required image
     * @param $image
     * @param null $imageQuery
     * @return null|string
     */
    public function __invoke($image, $imageQuery = NULL)
    {
        return $this->palette->getUrl($image, $imageQuery);
    }

}