<?php

namespace Brendt\Stitcher\Application;

use Brendt\Stitcher\App;
use PHPUnit\Framework\TestCase;

class DevControllerTest extends TestCase
{

    public function test_run() {
        $controller = App::init('./tests/config.yml')::get('app.dev.controller');
        $response = $controller->run('/');

        $this->assertContains('<html>', $response);
    }

    public function test_run_detail() {
        $controller = App::init('./tests/config.yml')::get('app.dev.controller');
        $response = $controller->run('/churches/church-a');

        $this->assertContains('<html>', $response);
    }

}
