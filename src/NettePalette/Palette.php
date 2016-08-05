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

use Nette\Utils\Strings;
use Palette\Generator\IPictureLoader;
use Palette\Picture;
use Palette\Generator\Server;
use Tracy\Debugger;

/**
 * Palette service implementation for Nette Framework
 * Class Palette
 * @package NettePalette
 */
class Palette
{
    /** @var Server */
    protected $generator;

    /** @var string|null */
    protected $websiteUrl;

    /** @var bool is used relative urls for images? */
    protected $isUrlRelative;

    /** @var bool catch exceptions? */
    protected $catchException;

    /** @var bool return fallback image on exception? */
    protected $fallbackImageOnException;

    /** @var bool|string exceptions? FALSE = no, TRUE = yes, string = only exception messages to log file */
    protected $logException;


    /**
     * Palette constructor.
     * @param string $storagePath absolute or relative path to generated thumbs (and pictures) directory
     * @param string $storageUrl absolute live url to generated thumbs (and pictures) directory
     * @param null|string $basePath absolute path to website root directory
     * @param null|string $fallbackImage absolute or relative path to default image.
     * @param null $templates palette image query templates
     * @param null|string $websiteUrl
     * @param IPictureLoader|NULL $pictureLoader
     */
    public function __construct($storagePath,
                                $storageUrl,
                                $basePath = NULL,
                                $fallbackImage = NULL,
                                $templates = NULL,
                                $websiteUrl = NULL,
                                IPictureLoader $pictureLoader = NULL)
    {
        // Setup image generator instance
        $this->generator = new Server($storagePath, $storageUrl, $basePath);

        // Register fallback image
        if($fallbackImage)
        {
            $this->generator->setFallbackImage($fallbackImage);
        }

        // Register defined image query templates
        if($templates && is_array($templates))
        {
            foreach ($templates as $templateName => $templateQuery)
            {
                $this->generator->setTemplateQuery($templateName, $templateQuery);
            }
        }

        // Set website url (optional)
        $this->websiteUrl = $websiteUrl;

        // Is used relative urls for images?
        $this->isUrlRelative =
            !Strings::startsWith($storageUrl, '//') &&
            !Strings::startsWith($storageUrl, 'http://') &&
            !Strings::startsWith($storageUrl, 'https://');

        // Set custom picture loader
        if($pictureLoader)
        {
            $this->generator->setPictureLoader($pictureLoader);
        }
    }


    /**
     * Set server image generation behavior on exception
     * @param bool $catch catch exceptions?
     * @param bool $fallbackToImage return fallback image on exception?
     * @param bool|string $log exceptions? FALSE = no, TRUE = yes, string = only exception messages to log file
     */
    public function setServerExceptionHandling($catch = FALSE, $fallbackToImage = FALSE, $log = FALSE)
    {
        $this->catchException = $catch;
        $this->fallbackImageOnException = $fallbackToImage;
        $this->logException = $log;
    }


    /**
     * Get absolute url to image with specified image query string
     * @param $image
     * @return null|string
     */
    public function __invoke($image)
    {
        return $this->generator->loadPicture($image)->getUrl();
    }


    /**
     * Get url to image with specified image query string
     * Supports absolute picture url when is relative generator url set
     * @param $image
     * @param null $imageQuery
     * @return null|string
     */
    public function getUrl($image, $imageQuery = NULL)
    {
        // Experimental support for absolute picture url when is relative generator url set
        if($imageQuery && Strings::startsWith($imageQuery, '//'))
        {
            $imageQuery = Strings::substring($imageQuery, 2);
            $imageUrl   = $this->getPictureGeneratorUrl($image, $imageQuery);

            if($this->isUrlRelative)
            {
                if($this->websiteUrl)
                {
                    return $this->websiteUrl . $imageUrl;
                }
                else
                {
                    return '//' . $_SERVER['SERVER_ADDR'] . $imageUrl;
                }
            }
            else
            {
                return $imageUrl;
            }
        }

        return $this->getPictureGeneratorUrl($image, $imageQuery);
    }


    /**
     * Get url to image with specified image query string from generator
     * @param $image
     * @param null $imageQuery
     * @return null|string
     */
    protected function getPictureGeneratorUrl($image, $imageQuery = NULL)
    {
        if(!is_null($imageQuery))
        {
            $image .= '@' . $imageQuery;
        }

        return $this->generator->loadPicture($image)->getUrl();
    }


    /**
     * Get Palette picture instance
     * @param $image
     * @return Picture
     */
    public function getPicture($image)
    {
        return $this->generator->loadPicture($image);
    }


    /**
     * Get Palette generator instance
     * @return Server
     */
    public function getGenerator()
    {
        return $this->generator;
    }


    /**
     * Execute palette service generator backend
     */
    public function serverResponse()
    {
        // Exceptions are catched
        if($this->catchException)
        {
            try
            {
                //$this->generator->serverResponse();
            }
            catch (\Exception $exception)
            {
                // Log catched exception
                if($this->logException)
                {
                    if(is_string($this->logException))
                    {
                        Debugger::log($exception->getMessage(), $this->logException);
                    }
                    else
                    {
                        Debugger::log($exception);
                    }
                }

                $fallbackImage = $this->generator->getFallbackImage();

                // Return fallback image
                if($this->fallbackImageOnException && $fallbackImage)
                {

                }

            }
        }
        // Exceptions are not catched
        else
        {
            //$this->generator->serverResponse();
        }
    }
    
}
