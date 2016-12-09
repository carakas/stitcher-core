<?php

use brendt\stitcher\factory\TemplateEngineFactory;
use brendt\stitcher\Config;
use brendt\stitcher\template\smarty\SmartyEngine;
use brendt\stitcher\template\twig\TwigEngine;

class TemplateEngineFactoryTest extends PHPUnit_Framework_TestCase {

    public function setUp() {
        Config::load('./tests', 'config.yml');
    }

    protected function createTemplateEngineFactory() {
        return new TemplateEngineFactory();
    }

    public function test_factory_smarty() {
        $factory = $this->createTemplateEngineFactory();

        $this->assertInstanceOf(SmartyEngine::class, $factory->getByType(TemplateEngineFactory::SMARTY_ENGINE));
    }

    public function test_factory_twig() {
        $factory = $this->createTemplateEngineFactory();

        $this->assertInstanceOf(TwigEngine::class, $factory->getByType(TemplateEngineFactory::TWIG_ENGINE));
    }

    /**
     * @expectedException brendt\stitcher\exception\UnknownEngineException
     */
    public function test_unknown_id_throws_exception() {
        $factory = $this->createTemplateEngineFactory();

        $factory->getByType('unknown');
    }

}
