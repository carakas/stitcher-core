<?php

namespace Brendt\Stitcher;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class StitcherTest extends TestCase
{
    /**
     * @param array $defaultConfig
     *
     * @return Stitcher
     */
    protected function createStitcher(array $defaultConfig = []) : Stitcher {
        return App::init('./tests/config.yml', $defaultConfig)::get('stitcher');
    }

    public function test_stitch() {
        $stitcher = $this->createStitcher();
        $blanket = $stitcher->stitch();

        $this->assertArrayHasKey('/', $blanket);
        $this->assertArrayHasKey('/churches', $blanket);
        $this->assertArrayHasKey('/churches/church-a', $blanket);

        foreach ($blanket as $page) {
            $this->assertContains("<html>", $page);
        }

        $this->assertArrayHasKey('/', $blanket);
        $this->assertTrue(strpos($blanket['/'], '<h1>') !== false);
    }

    public function test_stitch_single_route() {
        $stitcher = $this->createStitcher();
        $blanket = $stitcher->stitch('/churches/{id}');

        $this->assertArrayNotHasKey('/', $blanket);
        $this->assertArrayNotHasKey('/churches', $blanket);
        $this->assertArrayHasKey('/churches/church-a', $blanket);
    }

    public function test_stitch_multiple_routes() {
        $stitcher = $this->createStitcher();
        $blanket = $stitcher->stitch([
            '/churches/{id}',
            '/',
        ]);

        $this->assertArrayNotHasKey('/churches', $blanket);
        $this->assertArrayHasKey('/', $blanket);
        $this->assertArrayHasKey('/churches/church-a', $blanket);
    }

    public function test_stitch_detail_route() {
        $stitcher = $this->createStitcher();
        $blanket = $stitcher->stitch('/churches/{id}');

        $this->assertArrayHasKey('/churches/church-a', $blanket);
        $this->assertArrayHasKey('/churches/church-b', $blanket);
        $this->assertContains('Church A', $blanket['/churches/church-a']);
        $this->assertContains('Church B', $blanket['/churches/church-b']);
    }

    public function test_save() {
        $fs = new Filesystem();
        $fs->remove('././tests/public/index.html/public');

        $stitcher = $this->createStitcher();
        $blanket = $stitcher->stitch();
        $stitcher->save($blanket);

        $this->assertTrue($fs->exists("./tests/public/index.html"));
        $this->assertTrue($fs->exists("./tests/public/churches/church-a.html"));
        $this->assertTrue($fs->exists("./tests/public/churches/church-b.html"));
        $this->assertTrue($fs->exists("./tests/public/churches.html"));

        $finder = new Finder();
        /** @var SplFileInfo[] $files */
        $files = $finder->in("./tests/public/churches")->name('church-a.html');

        foreach ($files as $file) {
            $html = $file->getContents();

            $this->assertContains('Church A', $html);
        }
    }

    public function test_stitch_route_single() {
        $stitcher = $this->createStitcher();
        $blanket = $stitcher->stitch('/churches/{id}');

        foreach ($blanket as $page => $html) {
            $this->assertContains('Church', $html);
            $this->assertContains('Intro', $html);
        }

        $this->assertArrayHasKey('/churches/church-a', $blanket);
        $this->assertArrayHasKey('/churches/church-b', $blanket);
        $this->assertArrayNotHasKey('/churches', $blanket);
        $this->assertArrayNotHasKey('/', $blanket);
    }

    public function test_stitch_route_multiple() {
        $stitcher = $this->createStitcher();
        $blanket = $stitcher->stitch('/');

        $html = $blanket['/'];

        $this->assertContains('Church A', $html);
        $this->assertContains('Church B', $html);
        $this->assertContains('HOOOOME', $html);
    }

    public function test_stitch_with_twig() {
        $stitcher = $this->createStitcher(['engines.template' => 'twig']);
        $blanket = $stitcher->stitch('/churches/{id}');

        foreach ($blanket as $page => $html) {
            $this->assertContains('Church', $html);
            $this->assertContains('Intro', $html);
        }
    }

    public function test_sitemap_xml_save() {
        $stitcher = $this->createStitcher(['sitemap.url' => 'https://www.stitcher.io']);
        $stitcher->stitch();
        $stitcher->saveSitemap();

        $this->assertTrue(file_exists('./tests/public/sitemap.xml'));
        $xml = file_get_contents('./tests/public/sitemap.xml');

        $this->assertContains('<loc>https://www.stitcher.io/churches/church-b</loc>', $xml);
    }

    public function test_htaccess_save() {
        $stitcher = $this->createStitcher(['sitemap.url' => 'https://www.stitcher.io']);
        $stitcher->stitch();
        $stitcher->saveHtaccess();

        $this->assertTrue(file_exists('./tests/public/.htaccess'));
    }

    public function test_async() {
        $stitcher = $this->createStitcher(['async' => true]);
        $blanket = $stitcher->stitch('/');

        $this->assertEmpty($blanket);
    }
}
