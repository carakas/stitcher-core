<?php

namespace Brendt\Stitcher\Tests\Phpunit\Site;

use Brendt\Stitcher\Site\Page;
use Brendt\Stitcher\Site\Site;
use Brendt\Stitcher\Stitcher;
use PHPUnit\Framework\TestCase;

class SiteTest extends TestCase
{
    public function setUp() {
        Stitcher::create('./tests/config.yml');
    }

    private function createSite() {
        return new Site();
    }

    public function test_iteration() {
        $site = $this->createSite();
        $pageA = new Page('/a', ['template' => 'a']);
        $pageB = new Page('/b', ['template' => 'b']);

        $site->addPage($pageA);
        $site->addPage($pageB);

        $count = 0;

        foreach ($site as $page) {
            $this->assertInstanceOf(Page::class, $page);
            $count++;
        }

        $this->assertEquals(2, $count);
    }
}
