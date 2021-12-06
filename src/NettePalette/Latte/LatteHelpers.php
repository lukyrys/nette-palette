<?php declare(strict_types=1);

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

namespace NettePalette\Latte;

use Nette\Utils\Image;
use NettePalette\Palette;
use NettePalette\PictureUrl;
use Palette\Exception;
use Palette\Picture;

/**
 * Class LatteHelpers
 * @package NettePalette\Latte
 */
final class LatteHelpers
{
    /**
     * Vrací mimeType zadaného souboru s obrázkem.
     * @param Picture $picture
     * @return string|null
     */
    public static function getPictureMimeType(Picture $picture): ?string
    {
        $pictureType = Image::detectTypeFromFile($picture->getImage());

        return $pictureType ? Image::typeToMimeType($pictureType) : null;
    }


    /**
     * Vygeneruje HTML scrsetu pro picture.
     * @param Palette $palette
     * @param PictureUrl $pictureUrl
     * @return string
     * @throws Exception
     */
    public static function generatePictureSrcSetHtml(Palette $palette, PictureUrl $pictureUrl): string
    {
        $scrSets = [];

        // Zjistíme mimeType obrázku.
        $pictureMimeType = self::getPictureMimeType($pictureUrl->getPicture());

        // Vygenerování scrsetu mířící na výchozí obrázek pro prohlížeče, podporující tag picture.
        if ($pictureMimeType)
        {
            $scrSets[] = sprintf(
                '<source srcset="%s" type="%s">' . "\n",
                $pictureUrl->getPictureUrl(), // Tento obrázek je stejný s obrázkem, který se vypisuje do img tagu.
                $pictureMimeType
            );
        }

        // Vygenerování scrsetu mířící na obrázek ve formátu webP pro prohlížeče, podporující tag a WebP.
        if ($pictureMimeType !== 'image/webp' && !$pictureUrl->getPicture()->isWebp())
        {
            $scrSets[] = sprintf(
                '<source srcset="%s" type="image/webp">' . "\n",
                $palette->getUrl($pictureUrl->getImage(), $pictureUrl->getImageQuery() . '&WebP') // Do palette query přidáme transformaci na WebP.
            );
        }

        return implode('', $scrSets);
    }
}
