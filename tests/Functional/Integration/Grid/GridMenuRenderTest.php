<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Tests\Functional\Integration\Grid;

use App\MultiZonePage;
use Page;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Dev\FunctionalTest;
use WeDevelop\AdminToolbar\Tests\Integration\Integration\Grid\GridFixture;
use WeDevelop\Grid\Model\ContentElement;
use WeDevelop\Grid\Model\SharedBlock;

final class GridMenuRenderTest extends FunctionalTest
{
    use GridFixture;

    protected static $fixture_file = '../../../Integration/Integration/Grid/grid.yml';

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['grid_page', 'no_grid_page'] as $page) {
            $this->objFromFixture(Page::class, $page)->publishRecursive();
        }

        $this->objFromFixture(MultiZonePage::class, 'multi_page')->publishRecursive();
        $this->objFromFixture(SharedBlock::class, 'banner')->publishRecursive();

        $memberId = $this->logInWithPermission('ADMIN');
        $this->session()->set('loggedInAs', $memberId);
    }

    public function testGridPageRendersTheGridMenu(): void
    {
        $body = $this->body($this->objFromFixture(Page::class, 'grid_page'));
        $intro = $this->objFromFixture(ContentElement::class, 'intro');

        $this->assertStringContainsString('data-toggle-dialog="GridMenu"', $body);
        $this->assertStringContainsString('<dialog id="GridMenu"', $body);
        $this->assertStringContainsString('data-grid-zone="main"', $body);
        $this->assertStringContainsString('data-grid-node="section"', $body);
        $this->assertStringContainsString('data-grid-node="shared"', $body);
        $this->assertStringContainsString('Intro', $body);
        $this->assertStringContainsString(sprintf('href="%s"', $intro->getCMSEditLink()), $body);
        $this->assertStringContainsString('grid-template-columns: repeat(12, minmax(0, 1fr));', $body);
        $this->assertStringContainsString('grid-column: span 8 / span 8;', $body);
        $this->assertStringNotContainsString('Zone: main', $body);
    }

    public function testPageWithoutTheGridHasNoGridMenu(): void
    {
        $body = $this->body($this->objFromFixture(Page::class, 'no_grid_page'));

        $this->assertStringContainsString('id="admin-toolbar"', $body);
        $this->assertStringNotContainsString('data-toggle-dialog="GridMenu"', $body);
    }

    public function testEveryZoneRendersWithAHeading(): void
    {
        $body = $this->body($this->objFromFixture(MultiZonePage::class, 'multi_page'));

        $this->assertStringContainsString('data-grid-zone="main"', $body);
        $this->assertStringContainsString('data-grid-zone="sidebar"', $body);
        $this->assertStringContainsString('Zone: main', $body);
        $this->assertStringContainsString('Zone: sidebar', $body);
    }

    private function body(SiteTree $page): string
    {
        $response = $this->get($page->Link());
        $this->assertSame(200, $response->getStatusCode());

        return (string) $response->getBody();
    }
}
