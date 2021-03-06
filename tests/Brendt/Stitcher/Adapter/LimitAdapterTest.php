<?php

namespace Brendt\Stitcher\Adapter;

use Brendt\Stitcher\App;
use Brendt\Stitcher\Site\Page;
use Brendt\Stitcher\Stitcher;
use PHPUnit\Framework\TestCase;

class LimitAdapterTest extends TestCase
{
    /**
     * @return LimitAdapter
     */
    private function createAdapter() {
        App::init('./tests/config.yml');

        return App::get('adapter.limit');
    }

    private function createPage(int $limit = 2) {
        $page = new Page('/entries', [
            'template'  => 'home',
            'variables' => [
                'entries' => 'limit_entries.yml',
            ],
            'adapters'  => [
                'limit' => [
                    'entries'  => $limit,
                ],
            ],
        ]);

        return $page;
    }

    public function test_limit_adapter() {
        $page = $this->createPage(2);
        $adapter = $this->createAdapter();

        $adaptedPages = $adapter->transform($page);
        $adaptedPage = reset($adaptedPages);

        $entries = $adaptedPage->getVariable('entries');
        
        $this->assertCount(2, $entries);
    }
}
