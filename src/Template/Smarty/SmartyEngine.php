<?php

namespace Brendt\Stitcher\Template\Smarty;

use \Smarty;
use Brendt\Stitcher\Config;
use Brendt\Stitcher\Template\TemplateEngine;
use Symfony\Component\Finder\SplFileInfo;

/**
 * The Smarty template engine.
 */
class SmartyEngine extends Smarty implements TemplateEngine {

    /**
     * Create the Smarty engine, set the template- and cache directory; and add the plugin directory.
     */
    public function __construct() {
        parent::__construct();

        $templateFolder = Config::get('directories.template');
        $this->addTemplateDir($templateFolder);

        $this->setCompileDir(Config::get('directories.cache'));
        $this->addPluginsDir([__DIR__]);

        $this->caching = false;
    }

    /**
     * {@inheritdoc}
     */
    public function renderTemplate(SplFileInfo $template) {
        return $this->fetch($template->getRealPath());
    }

    /**
     * {@inheritdoc}
     */
    public function addTemplateVariables(array $variables) {
        foreach ($variables as $name => $variable) {
            $this->assign($name, $variable);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function clearTemplateVariables() {
        $this->clearAllAssign();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addTemplateVariable($name, $value) {
        $this->assign($name, $value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function clearTemplateVariable($variable) {
        $this->clearAssign($variable);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplateExtension() {
        return 'tpl';
    }
}