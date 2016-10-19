<?php

namespace brendt\stitcher\provider;

use brendt\stitcher\exception\ProviderException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class YamlProvider extends AbstractArrayProvider {

    public function parse($path = '*.yml') {
        $finder = new Finder();
        $data = [];
        if (!strpos($path, '.yml')) {
            $path .= '.yml';
        }

        $files = $finder->files()->in("{$this->root}")->path($path);

        foreach ($files as $file) {
            try {
                $parsed = Yaml::parse($file->getContents());

                if (isset($parsed['entries'])) {
                    $data += $parsed['entries'];
                } else {
                    $id = str_replace(".{$file->getExtension()}", '', $file->getFilename());
                    $data[$id] = $parsed;
                }
            } catch (ParseException $e) {
                throw new ProviderException("{$file->getRelativePathname()}: {$e->getMessage()}");
            }
        }

        $data = $this->parseArrayData($data);

        return $data;
    }

}