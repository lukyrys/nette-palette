<?php

namespace NettePalette;

use Nette\Application\UI\Presenter;

/**
 * Class PalettePresenter
 * @package App\Presenters
 */
class PalettePresenter extends Presenter {

    /**
     * @var Palette @inject
     */
    public $palette;


    /**
     * Pallete images backend render
     * @throws \Nette\Application\AbortException
     */
    public function actionImage() {

        $this->palette->getStorage()->serverResponse();
        $this->terminate();
    }

}