<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Tests\Integration\Integration\Grid;

use App\MultiZonePage;
use Page;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\Model\ArrayData;
use SilverStripe\Model\List\ArrayList;
use SilverStripe\Security\Member;
use WeDevelop\AdminToolbar\Integration\Grid\GridMenu;
use WeDevelop\AdminToolbar\ToolbarContext;
use WeDevelop\Grid\Adapter\TailwindAdapter;
use WeDevelop\Grid\Contract\GridAdapterInterface;
use WeDevelop\Grid\Model\ContentElement;
use WeDevelop\Grid\Model\Row;
use WeDevelop\Grid\Model\SharedBlock;

final class GridMenuTest extends SapphireTest
{
    use GridFixture;

    protected static $fixture_file = 'grid.yml';

    protected static $required_extensions = [
        ContentElement::class => [HiddenElementExtension::class],
    ];

    private Member $admin;

    private Member $toolbarOnly;

    protected function setUp(): void
    {
        parent::setUp();

        $this->untitleAside();
        $this->admin = $this->createMemberWithPermission('ADMIN');
        $this->toolbarOnly = $this->createMemberWithPermission('ADMIN_TOOLBAR');
    }

    public function testUnsupportedWithoutAPage(): void
    {
        $this->assertFalse($this->menu(null)->isSupported());
    }

    public function testUnsupportedForAPageWithoutTheExtension(): void
    {
        // The harness applies GridPageExtension to Page, not to SiteTree.
        $this->assertFalse($this->menu(SiteTree::create())->isSupported());
    }

    public function testUnsupportedWhenUseGridIsOff(): void
    {
        $this->assertFalse($this->menu($this->page('no_grid_page'))->isSupported());
    }

    public function testUnsupportedWithoutRoots(): void
    {
        $this->assertFalse($this->menu($this->page('empty_grid_page'))->isSupported());
    }

    public function testSupportedForAGridPageWithRoots(): void
    {
        $this->assertTrue($this->menu($this->page('grid_page'))->isSupported());
    }

    public function testNoZonesWithoutAGridPage(): void
    {
        $this->assertSame(0, $this->menu(null)->getZones()->count());
        $this->assertSame(0, $this->menu(SiteTree::create())->getZones()->count());
    }

    public function testSingleZoneHasNoHeading(): void
    {
        $zones = $this->menu($this->page('grid_page'))->getZones();

        $this->assertSame(['main'], $zones->column('Name'));
        $this->assertSame([false], $zones->column('ShowHeading'));
    }

    public function testZonesListMainFirstWithHeadings(): void
    {
        $page = $this->objFromFixture(MultiZonePage::class, 'multi_page');

        $zones = $this->menu($page)->getZones();

        $this->assertSame(['main', 'banner', 'sidebar'], $zones->column('Name'));
        $this->assertSame([true, true, true], $zones->column('ShowHeading'));
        $this->assertSame([['section', 'Main']], $this->kindsAndTitles($this->zoneNodes($zones, 'main')));
    }

    public function testTreeShape(): void
    {
        $this->assertSame(
            [
                ['Kind' => 'section', 'Title' => 'Hero', 'Span' => null, 'Of' => null, 'Children' => [
                    ['Kind' => 'row', 'Title' => 'Hero Row', 'Span' => null, 'Of' => 12, 'Children' => [
                        ['Kind' => 'column', 'Title' => 'Wide', 'Span' => 8, 'Of' => 12, 'Children' => [
                            ['Kind' => 'element', 'Title' => 'Intro', 'Span' => null, 'Of' => null, 'Children' => []],
                        ]],
                        ['Kind' => 'column', 'Title' => 'Narrow', 'Span' => 4, 'Of' => 12, 'Children' => [
                            ['Kind' => 'element', 'Title' => ContentElement::singleton()->getType(), 'Span' => null, 'Of' => null, 'Children' => []],
                        ]],
                    ]],
                ]],
                ['Kind' => 'shared', 'Title' => 'Banner', 'Span' => null, 'Of' => null, 'Children' => []],
            ],
            $this->tree($this->mainNodes($this->admin)),
        );
    }

    public function testUntitledElementFallsBackToItsType(): void
    {
        $narrow = $this->find($this->mainNodes($this->admin), ['section', 'row', 'column'], 1);

        $this->assertSame(['Content element'], $narrow->Children->column('Title'));
    }

    public function testSharedPlacementIsALeafLinkingToTheBlock(): void
    {
        $shared = $this->mainNodes($this->admin)->last();
        $this->assertInstanceOf(ArrayData::class, $shared);

        $this->assertSame('shared', $shared->Kind);
        $this->assertSame([], $this->tree($shared->Children));
        $this->assertSame($this->objFromFixture(SharedBlock::class, 'banner')->getCMSEditLink(), $shared->Link);
    }

    public function testLinksRequireEditPermission(): void
    {
        // A member without CMS access sees the published page only.
        $this->page('grid_page')->publishRecursive();
        $intro = $this->objFromFixture(ContentElement::class, 'intro');

        $adminLinks = $this->links($this->mainNodes($this->admin));
        $memberLinks = $this->links($this->mainNodes($this->toolbarOnly));

        $this->assertCount(7, $adminLinks);
        $this->assertNotContains(null, $adminLinks);
        $this->assertContains($intro->getCMSEditLink(), $adminLinks);
        $this->assertSame(array_fill(0, 7, null), $memberLinks);
    }

    public function testNodesTheMemberCannotViewAreOmitted(): void
    {
        Config::modify()->set(HiddenElementExtension::class, 'hidden_titles', ['Intro']);

        $tree = $this->tree($this->mainNodes($this->admin));

        $this->assertSame([], $tree[0]['Children'][0]['Children'][0]['Children']);
        $this->assertCount(1, $tree[0]['Children'][0]['Children'][1]['Children']);
    }

    public function testColumnCountFollowsTheAdapter(): void
    {
        Config::modify()->set(TailwindAdapter::class, 'total_columns', 16);
        // The adapter reads its column count once, when the Injector builds it.
        Injector::inst()->unregisterNamedObject(GridAdapterInterface::class);

        $row = $this->find($this->mainNodes($this->admin), ['section', 'row'], 0);

        $this->assertSame(16, $row->Of);
        $this->assertSame([16, 16], $row->Children->column('Of'));
        $this->assertSame([8, 4], $row->Children->column('Span'));
    }

    public function testIconsComeFromElementConfig(): void
    {
        Config::modify()->set(Row::class, 'icon', 'font-icon-test-row');

        $nodes = $this->mainNodes($this->admin);

        $this->assertSame(['font-icon-block-layout', 'font-icon-block-layout'], $nodes->column('Icon'));
        $this->assertSame('font-icon-test-row', $this->find($nodes, ['section', 'row'], 0)->Icon);
        $this->assertSame('font-icon-block-content', $this->find($nodes, ['section', 'row', 'column'], 0)->Icon);
        $this->assertSame('font-icon-block-content', $this->find($nodes, ['section', 'row', 'column', 'element'], 0)->Icon);
    }

    private function page(string $identifier): Page
    {
        return $this->objFromFixture(Page::class, $identifier);
    }

    private function menu(?SiteTree $page, ?Member $member = null): GridMenu
    {
        return GridMenu::create()->setContext(
            new ToolbarContext($page, $member ?? $this->admin, new HTTPRequest('GET', '/')),
        );
    }

    /**
     * @return ArrayList<ArrayData>
     */
    private function mainNodes(Member $member): ArrayList
    {
        return $this->zoneNodes($this->menu($this->page('grid_page'), $member)->getZones(), 'main');
    }

    /**
     * @param ArrayList<ArrayData> $zones
     * @return ArrayList<ArrayData>
     */
    private function zoneNodes(ArrayList $zones, string $name): ArrayList
    {
        $zone = $zones->find('Name', $name);
        $this->assertInstanceOf(ArrayData::class, $zone);

        return $zone->Nodes;
    }

    /**
     * Follows the first node of each listed kind down the tree; `$index` picks
     * among the siblings at the last level.
     *
     * @param ArrayList<ArrayData> $nodes
     * @param list<string> $path
     */
    private function find(ArrayList $nodes, array $path, int $index): ArrayData
    {
        $kind = array_pop($path);

        foreach ($path as $step) {
            $node = $nodes->find('Kind', $step);
            $this->assertInstanceOf(ArrayData::class, $node, $step);
            $nodes = $node->Children;
        }

        $found = array_values($nodes->filter('Kind', $kind)->toArray())[$index] ?? null;
        $this->assertInstanceOf(ArrayData::class, $found, (string) $kind);

        return $found;
    }

    /**
     * @param ArrayList<ArrayData> $nodes
     * @return list<array{Kind: mixed, Title: mixed, Span: mixed, Of: mixed, Children: list<mixed>}>
     */
    private function tree(ArrayList $nodes): array
    {
        $tree = [];

        foreach ($nodes as $node) {
            $tree[] = [
                'Kind' => $node->Kind,
                'Title' => $node->Title,
                'Span' => $node->Span,
                'Of' => $node->Of,
                'Children' => $this->tree($node->Children),
            ];
        }

        return $tree;
    }

    /**
     * @param ArrayList<ArrayData> $nodes
     * @return list<array{mixed, mixed}>
     */
    private function kindsAndTitles(ArrayList $nodes): array
    {
        return array_map(static fn (array $node): array => [$node['Kind'], $node['Title']], $this->tree($nodes));
    }

    /**
     * @param ArrayList<ArrayData> $nodes
     * @return list<mixed>
     */
    private function links(ArrayList $nodes): array
    {
        $links = [];

        foreach ($nodes as $node) {
            $links = [...$links, $node->Link, ...$this->links($node->Children)];
        }

        return $links;
    }
}
