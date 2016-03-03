<?php

namespace NettePalette;

use Palette\Picture;
use Palette\ServerStorage;

/**
 * Class Palette
 * @package Palette
 */
class Palette {

    protected $storage;


    public function __construct($storagePath, $storageUrl, $basePath = NULL)  {

        $this->storage = new ServerStorage($storagePath, $storageUrl, $basePath);
    }




    public function __invoke($image)
    {
        return $this->storage->loadPicture($image)->getUrl();
    }


    /**
     * Vrací absolutní url adresu k obrázku se zadanými efekty
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
     * Vrací objekt samotný objekt obrázku
     * @param $image
     * @return Picture
     */
    public function getPicture($image) {

        return $this->storage->loadPicture($image);
    }


    /**
     * Vrací objekt úložiště obrázků miniatur
     * @return ServerStorage
     */
    public function getStorage() {

        return $this->storage;
    }


}