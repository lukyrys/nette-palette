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

use Nette\Application\UI\Presenter;

/**
 * Palette Presenter for on demand generation images with palette
 * Class PalettePresenter
 * @package App\Presenters
 */
class PalettePresenter extends Presenter
{
    /**
     * Pallete images backend render
     * @throws \Nette\Application\AbortException
     */
    public function actionImage()
    {
        /** @var $palette Palette */
        $palette = $this->context->getService('palette.service');
        $palette->serverResponse();

        $this->terminate();
    }
}
