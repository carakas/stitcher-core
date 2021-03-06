<?php

namespace Brendt\Stitcher\Site\Http;

use Brendt\Stitcher\Lib\Browser;
use Brendt\Stitcher\Site\Page;
use PHPUnit\Framework\TestCase;

class HtaccessTest extends TestCase
{
    private function getBrowser() : Browser {
        return new Browser('./tests/src', './tests/public', './tests/src/template', './tests/.cache');
    }

    /**
     * @test
     */
    public function it_can_load_the_file() {
        $htaccess = new Htaccess($this->getBrowser());

        $this->assertNotNull($htaccess);
    }

    /**
     * @test
     */
    public function it_can_create_a_page_block() {
        $htaccess = new Htaccess($this->getBrowser());

        $page = new Page('/blog/read', ['template' => 'blog/overview']);
        $htaccess->getPageBlock($page);

        $this->assertContains('<filesmatch "^read\.html$">', $htaccess->parse());
    }

    /**
     * @test
     */
    public function it_adds_index_option() {
        $htaccess = new Htaccess($this->getBrowser());

        $this->assertContains('Options -Indexes', $htaccess->parse());
    }

    /**
     * @test
     */
    public function it_can_clear_page_blocks() {
        $htaccess = new Htaccess($this->getBrowser());

        $htaccess->clearPageBlocks();

        $this->assertNotContains('<FilesMatch', $htaccess->parse());
    }

    /**
     * @test
     */
    public function it_can_parse_https_rewrite() {
        $htaccess = new Htaccess($this->getBrowser());
        $htaccess->setRedirectHttps(true);

        $parsed = $htaccess->parse();

        $this->assertContains('RewriteCond %{HTTPS} off', $parsed);
        $this->assertContains('RewriteRule (.*) https://%{HTTP_HOST}%{REQUEST_URI} [R=301,L]', $parsed);
    }

    /**
     * @test
     */
    public function it_can_parse_www_rewrite() {
        $htaccess = new Htaccess($this->getBrowser());
        $htaccess->setRedirectWww(true);

        $parsed = $htaccess->parse();

        $this->assertContains('RewriteCond %{HTTP_HOST} !^www\.', $parsed);
        $this->assertContains('RewriteRule ^(.*)$ http://www.%{HTTP_HOST}/$1 [R=301,L]', $parsed);
    }

    /**
     * @test
     */
    public function it_always_parses_html_rewrite() {
        $htaccess = new Htaccess($this->getBrowser());

        $parsed = $htaccess->parse();

        $this->assertContains('RewriteCond %{DOCUMENT_ROOT}/$1.html -f', $parsed);
        $this->assertContains('RewriteRule ^(.+?)/?$ /$1.html [L]', $parsed);
    }

    /**
     * @test
     */
    public function it_keeps_default_rewrite_options() {
        $htaccess = new Htaccess($this->getBrowser());

        $parsed = $htaccess->parse();

        $this->assertContains('RewriteEngine On', $parsed);
        $this->assertContains('DirectorySlash Off', $parsed);
    }

    /**
     * @test
     */
    public function it_parses_www_rewrite_before_https() {
        $htaccess = new Htaccess($this->getBrowser());
        $htaccess->setRedirectWww(true);
        $htaccess->setRedirectHttps(true);

        $parsed = $htaccess->parse();

        $this->assertContains('RewriteRule ^(.*)$ http://www.%{HTTP_HOST}/$1 [R=301,L]
    RewriteCond %{HTTPS} off', $parsed);
    }
}
