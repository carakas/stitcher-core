<?php

namespace brendt\stitcher\parser;

use brendt\stitcher\Config;
use brendt\stitcher\factory\ParserFactory;

/**
 * The AbstractArrayParser class provides the abstraction needed to parse arrays of entries provided by
 * eg. the Yaml- or JsonParser.
 *
 * @see \brendt\stitcher\parser\YamlParser
 * @see \brendt\stitcher\parser\JsonParser
 */
abstract class AbstractArrayParser implements Parser {

    /**
     * @var ParserFactory
     */
    protected $parserFactory;

    /**
     * AbstractParser constructor.
     */
    public function __construct() {
        $this->parserFactory = Config::getDependency('factory.parser');
    }

    /**
     * Parse an array of entries loaded from eg. the Yaml- or JsonParser
     *
     * @param array $data
     *
     * @return mixed
     *
     * @see \brendt\stitcher\parser\YamlParser
     * @see \brendt\stitcher\parser\JsonParser
     */
    protected function parseArrayData(array $data) {
        $result = [];

        foreach ($data as $id => $entry) {
            $result[$id] = $this->parseEntryData($id, $entry);
        }

        return $result;
    }

    /**
     * Parse a single entry. An entry has multiple fields with each of them a value. This value can either be a path
     * to another data entry which will be parsed (using the ParserFactory); an array with a key `src` set,
     * which refers to another data entry; or normal data which will be kept the way it was provided.
     *
     * After parsing all fields, an additional check is performed which sets the entry's ID if it wasn't set yet.
     * Finally, an array with parsed fields, representing the entry, is returned.
     *
     * @param $id
     * @param $entry
     *
     * @return array
     *
     * @see \brendt\stitcher\factory\ParserFactory
     */
    protected function parseEntryData($id, $entry) {
        foreach ($entry as $field => $value) {
            if (is_string($value) && preg_match('/.*\.(md|jpg|png|json|yml)$/', $value) > 0) {
                $parser = $this->parserFactory->getParser($value);

                if (!$parser) {
                    continue;
                }

                $entry[$field] = $parser->parse(trim($value, '/'));
            } elseif (is_array($value) && array_key_exists('src', $value)) {
                $src = $value['src'];
                $parser = $this->parserFactory->getParser($src);

                if (!$parser) {
                    continue;
                }

                $entry[$field] = $parser->parse($value);
            }

            if (!isset($entry['id'])) {
                $entry['id'] = $id;
            }
        }

        return $entry;
    }

}