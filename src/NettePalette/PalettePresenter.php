<?php

namespace NettePalette;

use Nette\Application\UI\Presenter;

/**
 * Class PalettePresenter
 * @package App\Presenters
 */
class PalettePresenter extends Presenter {

    /**
     * Pallete images backend render
     * @throws \Nette\Application\AbortException
     */
    public function actionImage() {

        /**
         * @var $palette Palette
         */
        $palette = $this->context->getService('palette.service');
        $palette->getStorage()->serverResponse();

        $this->terminate();
    }

}