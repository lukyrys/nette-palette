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
use Palette\Generator\Server;

/**
 * Palette service implementation for Nette Framework
 * Class Palette
 * @package NettePalette
 */
class Palette {

    /**
     * @var Server
     */
    protected $generator;


    /**
     * Palette constructor.
     * @param string $storagePath absolute or relative path to generated thumbs (and pictures) directory
     * @param string $storageUrl absolute live url to generated thumbs (and pictures) directory
     * @param null|string $basePath absolute path to website root directory
     * @param null|string $fallbackImage absolute or relative path to default image.
     * @param null $templates palette image query templates
     */
    public function __construct($storagePath,
                                $storageUrl,
                                $basePath = NULL,
                                $fallbackImage = NULL,
                                $templates = NULL)  {

        $this->generator = new Server($storagePath, $storageUrl, $basePath);

        // REGISTER DEFINED IMAGE QUERY TEMPLATES
        if($templates && is_array($templates)) {

            foreach ($templates as $templateName => $templateQuery) {

                $this->generator->setTemplateQuery($templateName, $templateQuery);
            }
        }

        if($fallbackImage) {

            $this->generator->setFallbackImage($fallbackImage);
        }
    }


    /**
     * Get absolute url to image with specified image query string
     * @param $image
     * @return null|string
     */
    public function __invoke($image) {

        return $this->generator->loadPicture($image)->getUrl();
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

        return $this->generator->loadPicture($image)->getUrl();
    }


    /**
     * Get Palette picture instance
     * @param $image
     * @return Picture
     */
    public function getPicture($image) {

        return $this->generator->loadPicture($image);
    }


    /**
     * Get Palette generator instance
     * @return Server
     */
    public function getGenerator() {

        return $this->generator;
    }
    
}