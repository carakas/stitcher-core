<?php

namespace brendt\stitcher\parser;

use brendt\stitcher\Config;
use Leafo\ScssPhp\Compiler;
use Symfony\Component\Finder\Finder;

class SassParser implements Parser {

    /**
     * @param $path
     *
     * @return string
     */
    public function parse($path) {
        /** @var Compiler $sass */
        $sass = Config::getDependency('engine.sass');
        $finder = new Finder();
        $files = $finder->files()->in(Config::get('directories.src'))->path(trim($path, '/'));
        $data = '';

        foreach ($files as $file) {
            $data .= $sass->compile($file->getContents());
        }

        return $data;
    }

}
