<?php

namespace NettePalette;

use Nette\DI\CompilerExtension;
use Nette\DI\ServiceCreationException;

/**
 * Class PaletteExtension
 * @package Palette
 */
class PaletteExtension extends CompilerExtension {

    /**
     * Processes configuration data.
     * @return void
     * @throws ServiceCreationException
     */
    public function loadConfiguration() {

        // LOAD EXTENSION CONFIGURATION
        $config = $this->getConfig();

        if(!isset($config['path'])) {

            throw new ServiceCreationException('Missing required path parameter in PaletteExtension configuration');
        }

        if(!isset($config['url'])) {

            throw new ServiceCreationException('Missing required url parameter in PaletteExtension configuration');
        }

        // REGISTER EXTENSION SERVICES
        $builder = $this->getContainerBuilder();

        // REGISTER PALETTE SERVICE
        $builder->addDefinition($this->prefix('service'))
                ->setClass('NettePalette\Palette', array($config['path'], $config['url'], $config['basepath']));

        // REGISTER LATTE MACRO SERVICE
        $builder->addDefinition($this->prefix('filter'))
                ->setClass('NettePalette\LatteFilter', [$this->prefix('@service')]);

        // REGISTER LATTE FILTER
        $this->getLatteService()
             ->addSetup('addFilter', ['palette', $this->prefix('@filter')]);

        // REGISTER EXTENSION PRESENTER
        $builder->getDefinition('nette.presenterFactory')
                ->addSetup('setMapping', [['Palette' => 'NettePalette\*Presenter']]);
    }


    /**
     * Get Latte service definition
     * @return \Nette\DI\ServiceDefinition
     */
    protected function getLatteService() {

        $builder = $this->getContainerBuilder();

        return $builder->hasDefinition('nette.latteFactory')
            ? $builder->getDefinition('nette.latteFactory')
            : $builder->getDefinition('nette.latte');
    }

}