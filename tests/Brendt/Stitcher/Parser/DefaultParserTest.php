<?php

namespace Brendt\Stitcher\Parser;

use PHPUnit\Framework\TestCase;

class DefaultParserTest extends TestCase
{

    /**
     * @test
     */
    public function it_can_parse_a_value_and_return_that_value() {
        $parser = new DefaultParser();
        $path = 'my_test';

        $this->assertEquals($path, $parser->parse($path));
    }

}
