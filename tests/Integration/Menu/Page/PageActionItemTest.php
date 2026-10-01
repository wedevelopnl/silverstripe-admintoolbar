<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Tests\Integration\Menu\Page;

use PHPUnit\Framework\Attributes\DataProvider;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Core\Config\Config;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\Security\Member;
use SilverStripe\Versioned\Versioned;
use WeDevelop\AdminToolbar\Menu\Page\ArchivePageItem;
use WeDevelop\AdminToolbar\Menu\Page\PageActionItem;
use WeDevelop\AdminToolbar\Menu\Page\UnpublishAndArchivePageItem;
use WeDevelop\AdminToolbar\Menu\Page\UnpublishPageItem;
use WeDevelop\AdminToolbar\Tests\Integration\Fixture\FixturePages;
use WeDevelop\AdminToolbar\ToolbarContext;

final class PageActionItemTest extends SapphireTest
{
    use FixturePages;

    protected static $fixture_file = '../../Fixture/pages.yml';

    private Member $admin;

    private Member $toolbarOnly;

    protected function setUp(): void
    {
        parent::setUp();

        $this->publishFixturePages();
        $this->admin = $this->createMemberWithPermission('ADMIN');
        $this->toolbarOnly = $this->createMemberWithPermission('ADMIN_TOOLBAR');
    }

    /**
     * @return iterable<string, array{class-string<PageActionItem>, ?string, string, bool}>
     */
    public static function supportMatrix(): iterable
    {
        yield 'unpublish a published page as admin' => [UnpublishPageItem::class, 'published', 'admin', true];
        yield 'unpublish a modified page as admin' => [UnpublishPageItem::class, 'modified', 'admin', true];
        yield 'unpublish a draft page as admin' => [UnpublishPageItem::class, 'draft', 'admin', false];
        yield 'unpublish a published page without page permissions' => [UnpublishPageItem::class, 'published', 'toolbarOnly', false];
        yield 'archive a draft page as admin' => [ArchivePageItem::class, 'draft', 'admin', true];
        yield 'archive a draft page without page permissions' => [ArchivePageItem::class, 'draft', 'toolbarOnly', false];
        yield 'archive a published page as admin' => [ArchivePageItem::class, 'published', 'admin', false];
        yield 'archive a modified page as admin' => [ArchivePageItem::class, 'modified', 'admin', false];
        yield 'unpublish and archive a published page as admin' => [UnpublishAndArchivePageItem::class, 'published', 'admin', true];
        yield 'unpublish and archive a modified page as admin' => [UnpublishAndArchivePageItem::class, 'modified', 'admin', true];
        yield 'unpublish and archive a draft page as admin' => [UnpublishAndArchivePageItem::class, 'draft', 'admin', false];
        yield 'unpublish and archive a published page without page permissions' => [UnpublishAndArchivePageItem::class, 'published', 'toolbarOnly', false];
        yield 'unpublish without a page' => [UnpublishPageItem::class, null, 'admin', false];
        yield 'archive without a page' => [ArchivePageItem::class, null, 'admin', false];
        yield 'unpublish and archive without a page' => [UnpublishAndArchivePageItem::class, null, 'admin', false];
    }

    /**
     * @param class-string<PageActionItem> $itemClass
     */
    #[DataProvider('supportMatrix')]
    public function testSupportFollowsPageStateAndPermission(string $itemClass, ?string $page, string $member, bool $supported): void
    {
        $context = $this->context(
            $page === null ? null : $this->fixturePage($page),
            $member === 'admin' ? $this->admin : $this->toolbarOnly,
        );

        $this->assertSame($supported, $itemClass::create()->setContext($context)->isSupported());
    }

    public function testUnpublishPerformRemovesTheLiveVersionOnly(): void
    {
        $page = $this->fixturePage('published');

        UnpublishPageItem::create()->perform($page);

        $this->assertFalse($this->isOnStage(Versioned::LIVE, $page->ID));
        $this->assertTrue($this->isOnStage(Versioned::DRAFT, $page->ID));
    }

    public function testArchivePerformArchivesTheDraft(): void
    {
        $page = $this->fixturePage('draft');

        ArchivePageItem::create()->perform($page);

        $this->assertTrue($page->isArchived());
        $this->assertFalse($this->isOnStage(Versioned::DRAFT, $page->ID));
    }

    public function testUnpublishAndArchivePerformRemovesBothStages(): void
    {
        $page = $this->fixturePage('published');

        UnpublishAndArchivePageItem::create()->perform($page);

        $this->assertFalse($this->isOnStage(Versioned::LIVE, $page->ID));
        $this->assertFalse($this->isOnStage(Versioned::DRAFT, $page->ID));
    }

    public function testForActionResolvesEachBuiltInAction(): void
    {
        $this->assertInstanceOf(UnpublishPageItem::class, PageActionItem::forAction('unpublish'));
        $this->assertInstanceOf(ArchivePageItem::class, PageActionItem::forAction('archive'));
        $this->assertInstanceOf(UnpublishAndArchivePageItem::class, PageActionItem::forAction('unpublishAndArchive'));
        $this->assertNull(PageActionItem::forAction('delete'));
        $this->assertNull(PageActionItem::forAction(''));
    }

    public function testForActionIgnoresDisabledItems(): void
    {
        Config::modify()->set(ArchivePageItem::class, 'enabled', false);

        $this->assertNull(PageActionItem::forAction('archive'));
    }

    public function testSuccessMessagesAndDestructiveFlags(): void
    {
        $unpublish = UnpublishPageItem::create();
        $archive = ArchivePageItem::create();
        $both = UnpublishAndArchivePageItem::create();

        $this->assertSame(['Page unpublished', false], [$unpublish->getSuccessMessage(), $unpublish->isDestructive()]);
        $this->assertSame(['Page archived', true], [$archive->getSuccessMessage(), $archive->isDestructive()]);
        $this->assertSame(['Page unpublished and archived', true], [$both->getSuccessMessage(), $both->isDestructive()]);
    }

    public function testPageIdComesFromTheContextPage(): void
    {
        $page = $this->fixturePage('published');

        $item = UnpublishPageItem::create()->setContext($this->context($page, $this->admin));

        $this->assertSame($page->ID, $item->getPageID());
    }

    private function context(?SiteTree $page, Member $member): ToolbarContext
    {
        return new ToolbarContext($page, $member, new HTTPRequest('GET', '/'));
    }
}
