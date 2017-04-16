<?php

namespace Brendt\Stitcher\Factory;

use Brendt\Stitcher\Exception\UnknownEngineException;
use Brendt\Stitcher\Template\TemplateEngine;
use Symfony\Component\DependencyInjection\ContainerInterface;

class TemplateEngineFactory
{
    const SMARTY_ENGINE = 'smarty';
    const TWIG_ENGINE = 'twig';

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var string
     */
    private $templateEngine;

    /**
     * TemplateEngineFactory constructor.
     *
     * @param ContainerInterface $container
     * @param string             $templateEngine
     */
    public function __construct(ContainerInterface $container, string $templateEngine) {
        $this->container = $container;
        $this->templateEngine = $templateEngine;
    }

    /**
     * @param $type
     *
     * @return mixed
     *
     * @throws UnknownEngineException
     */
    public function getByType($type) : TemplateEngine {
        switch ($type) {
            case self::TWIG_ENGINE:
                return $this->container->get('service.twig');
            case self::SMARTY_ENGINE:
                return $this->container->get('service.smarty');
            default:
                throw new UnknownEngineException();
        }
    }

    /**
     * @return TemplateEngine
     */
    public function getDefault() : TemplateEngine {
        return $this->getByType($this->templateEngine);
    }

}
