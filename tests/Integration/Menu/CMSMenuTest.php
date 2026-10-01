<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Tests\Integration\Menu;

use SilverStripe\Control\HTTPRequest;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\Security\Member;
use WeDevelop\AdminToolbar\Menu\CMSMenu;
use WeDevelop\AdminToolbar\ToolbarContext;

final class CMSMenuTest extends SapphireTest
{
    protected $usesDatabase = true;

    public function testSupportedForACMSUser(): void
    {
        $menu = $this->menuFor($this->logInWithPermission('ADMIN'));

        $this->assertTrue($menu->isSupported());
        $this->assertGreaterThan(0, $menu->getMainMenu()->count());
    }

    public function testUnsupportedWhenTheMainMenuIsEmpty(): void
    {
        $this->assertFalse($this->menuFor($this->logInWithPermission('ADMIN_TOOLBAR'))->isSupported());
    }

    private function menuFor(int $memberId): CMSMenu
    {
        $member = Member::get()->byID($memberId);
        $this->assertInstanceOf(Member::class, $member);

        return CMSMenu::create()->setContext(new ToolbarContext(null, $member, new HTTPRequest('GET', '/')));
    }
}
