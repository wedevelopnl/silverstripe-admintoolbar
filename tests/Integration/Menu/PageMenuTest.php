<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Tests\Integration\Menu;

use SilverStripe\Admin\SecurityAdmin;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\Security\Member;
use WeDevelop\AdminToolbar\Menu\Page\ArchivePageItem;
use WeDevelop\AdminToolbar\Menu\Page\UnpublishAndArchivePageItem;
use WeDevelop\AdminToolbar\Menu\Page\UnpublishPageItem;
use WeDevelop\AdminToolbar\Menu\PageMenu;
use WeDevelop\AdminToolbar\Model\MenuItem;
use WeDevelop\AdminToolbar\Tests\Integration\Fixture\FixturePages;
use WeDevelop\AdminToolbar\ToolbarContext;

final class PageMenuTest extends SapphireTest
{
    use FixturePages;

    protected static $fixture_file = '../Fixture/pages.yml';

    private Member $admin;

    private Member $toolbarOnly;

    protected function setUp(): void
    {
        parent::setUp();

        $this->publishFixturePages();
        $this->admin = $this->createMemberWithPermission('ADMIN');
        $this->toolbarOnly = $this->createMemberWithPermission('ADMIN_TOOLBAR');
    }

    public function testUnsupportedWithoutAPage(): void
    {
        $this->assertFalse($this->menu(null, $this->admin)->isSupported());
    }

    public function testSupportedWithAPage(): void
    {
        $this->assertTrue($this->menu($this->fixturePage('published'), $this->admin)->isSupported());
    }

    public function testPublishBadgeFollowsThePageState(): void
    {
        foreach (['published' => ['Published', 'green'], 'modified' => ['Modified', 'orange'], 'draft' => ['Draft', 'blue']] as $page => $expected) {
            $badge = $this->menu($this->fixturePage($page), $this->admin)->getPublishBadge();

            $this->assertSame($expected, [$badge->Label, $badge->Color], $page);
        }
    }

    public function testEditLinkForAnEditor(): void
    {
        $page = $this->fixturePage('published');

        $this->assertSame($page->getCMSEditLink(), $this->menu($page, $this->admin)->getEditLink());
    }

    public function testNoEditLinkWithoutEditPermission(): void
    {
        $this->assertNull($this->menu($this->fixturePage('published'), $this->toolbarOnly)->getEditLink());
    }

    public function testAuthorIsTheMemberWhoWroteTheVersion(): void
    {
        $page = $this->authoredPage();

        $this->assertSame($this->admin->getName(), $this->menu($page, $this->admin)->getAuthorName());
    }

    public function testAuthorLinkOnlyForSecurityAdminViewers(): void
    {
        $page = $this->authoredPage();

        $this->assertSame(
            SecurityAdmin::singleton()->getCMSEditLinkForManagedDataObject($this->admin),
            $this->menu($page, $this->admin)->getAuthorLink(),
        );
        $this->assertNull($this->menu($page, $this->toolbarOnly)->getAuthorLink());
    }

    public function testUnknownAuthorLabel(): void
    {
        // A version written while nobody is logged in carries AuthorID 0.
        $this->logOut();
        $page = $this->fixturePage('draft');
        $page->Title = 'Edited anonymously';
        $page->write();

        $menu = $this->menu($this->fixturePage('draft'), $this->admin);

        $this->assertSame('Unknown author', $menu->getAuthorName());
        $this->assertNull($menu->getAuthorLink());
    }

    public function testNoAuthorWithoutAPage(): void
    {
        $menu = $this->menu(null, $this->admin);

        $this->assertSame('Unknown author', $menu->getAuthorName());
        $this->assertNull($menu->getAuthorLink());
    }

    public function testItemsAreTheApplicablePageActions(): void
    {
        $this->assertSame(
            [UnpublishPageItem::class, UnpublishAndArchivePageItem::class],
            $this->itemClasses($this->menu($this->fixturePage('published'), $this->admin)),
        );
        $this->assertSame(
            [ArchivePageItem::class],
            $this->itemClasses($this->menu($this->fixturePage('draft'), $this->admin)),
        );
    }

    private function authoredPage(): SiteTree
    {
        $this->logInAs($this->admin);
        $page = $this->fixturePage('draft');
        $page->Title = 'Edited by the admin';
        $page->write();
        $this->logOut();

        return $this->fixturePage('draft');
    }

    private function menu(?SiteTree $page, Member $member): PageMenu
    {
        return PageMenu::create()->setContext(new ToolbarContext($page, $member, new HTTPRequest('GET', '/')));
    }

    /**
     * @return list<string>
     */
    private function itemClasses(PageMenu $menu): array
    {
        return array_values(array_map(static fn (MenuItem $item): string => $item::class, $menu->getItems()->toArray()));
    }
}
