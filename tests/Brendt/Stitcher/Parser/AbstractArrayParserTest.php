<?php

namespace Brendt\Stitcher\Parser;

use Brendt\Stitcher\App;
use Brendt\Stitcher\Stitcher;
use PHPUnit\Framework\TestCase;

class AbstractArrayParserTest extends TestCase
{

    public function setUp() {
        App::init('./tests/config.yml');
    }

    public function test_parse_normal_array() {
        $parser = new ArrayParserMock(App::get('factory.parser'));
        $result = $parser->parseEntryData('test', [
            'test' => [
                'title' => 'title',
                'body'  => 'body',
            ],
        ]);

        $this->assertTrue(isset($result['test']['title']));
        $this->assertTrue(isset($result['test']['body']));
    }

}

class ArrayParserMock extends AbstractArrayParser
{
    public function parse($path) {
        return;
    }

    public function parseEntryData($id, $entry) {
        return parent::parseEntryData($id, $entry);
    }
}
