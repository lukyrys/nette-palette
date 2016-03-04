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

use Palette\Picture;
use Palette\ServerStorage;

/**
 * Palette service implementation for Nette Framework
 * Class Palette
 * @package NettePalette
 */
class Palette {

    /**
     * @var ServerStorage
     */
    protected $storage;


    /**
     * Palette constructor.
     * @param string $storagePath absolute or relative path to generated thumbs (and pictures) directory
     * @param string $storageUrl absolute live url to generated thumbs (and pictures) directory
     * @param null|string $basePath absolute path to website root directory
     */
    public function __construct($storagePath, $storageUrl, $basePath = NULL)  {

        $this->storage = new ServerStorage($storagePath, $storageUrl, $basePath);
    }


    /**
     * Get absolute url to image with specified image query string
     * @param $image
     * @return null|string
     */
    public function __invoke($image) {

        return $this->storage->loadPicture($image)->getUrl();
    }


    /**
     * Get absolute url to image with specified image query string
     * @param $image
     * @param null $imageQuery
     * @return null|string
     */
    public function getUrl($image, $imageQuery = NULL) {

        if(!is_null($imageQuery)) {

            $image .= '@' . $imageQuery;
        }

        return $this->storage->loadPicture($image)->getUrl();
    }


    /**
     * Get Palette picture instance
     * @param $image
     * @return Picture
     */
    public function getPicture($image) {

        return $this->storage->loadPicture($image);
    }


    /**
     * Get Palette images storage instance
     * @return ServerStorage
     */
    public function getStorage() {

        return $this->storage;
    }
    
}