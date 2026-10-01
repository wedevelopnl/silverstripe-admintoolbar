<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Tests\Integration\Menu;

use SilverStripe\Admin\CMSProfileController;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\Security\Member;
use SilverStripe\Security\Security;
use WeDevelop\AdminToolbar\Menu\User\EditUserItem;
use WeDevelop\AdminToolbar\Menu\User\UsernameItem;
use WeDevelop\AdminToolbar\Menu\UserMenu;
use WeDevelop\AdminToolbar\Model\Menu;
use WeDevelop\AdminToolbar\Model\MenuItem;
use WeDevelop\AdminToolbar\ToolbarContext;

final class UserMenuTest extends SapphireTest
{
    protected $usesDatabase = true;

    public function testPlacedAtTheEnd(): void
    {
        $this->assertSame(Menu::PLACEMENT_END, UserMenu::create()->getPlacement());
    }

    public function testLogoutLink(): void
    {
        $this->assertSame(Security::logout_url(), UserMenu::create()->getLogoutLink());
    }

    public function testMemberIsTheContextMember(): void
    {
        $member = $this->createMemberWithPermission('ADMIN_TOOLBAR');

        $this->assertSame($member, UserMenu::create()->setContext($this->context($member))->getMember());
    }

    public function testUsernameItemShowsTheMemberName(): void
    {
        $member = $this->createMemberWithPermission('ADMIN_TOOLBAR');

        $item = UsernameItem::create()->setContext($this->context($member));

        $this->assertSame($member->getName(), $item->getMemberName());
    }

    public function testEditProfileItemForACMSUser(): void
    {
        $item = EditUserItem::create()->setContext($this->contextForLoggedIn('ADMIN'));

        $this->assertTrue($item->isSupported());
        $this->assertSame(CMSProfileController::singleton()->Link(), $item->getLink());
    }

    public function testNoEditProfileItemWithoutCMSAccess(): void
    {
        $item = EditUserItem::create()->setContext($this->contextForLoggedIn('ADMIN_TOOLBAR'));

        $this->assertFalse($item->isSupported());
    }

    public function testItemsAreUsernameThenEditProfile(): void
    {
        $menu = UserMenu::create()->setContext($this->contextForLoggedIn('ADMIN'));

        $this->assertSame(
            [UsernameItem::class, EditUserItem::class],
            array_values(array_map(static fn (MenuItem $item): string => $item::class, $menu->getItems()->toArray())),
        );
    }

    private function context(Member $member): ToolbarContext
    {
        return new ToolbarContext(null, $member, new HTTPRequest('GET', '/'));
    }

    /** The toolbar renders for the logged-in member; CMSProfileController::canView() relies on it. */
    private function contextForLoggedIn(string $permission): ToolbarContext
    {
        $member = $this->createMemberWithPermission($permission);
        $this->logInAs($member);

        return $this->context($member);
    }
}
