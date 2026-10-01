<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Tests\Functional;

use Page;
use SilverStripe\Admin\AdminRootController;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\Core\Config\Config;
use SilverStripe\Dev\FunctionalTest;
use SilverStripe\Security\Member;
use SilverStripe\Security\Permission;
use WeDevelop\AdminToolbar\AdminToolbar;
use WeDevelop\AdminToolbar\Button\FlushCacheButton;

final class ToolbarRenderTest extends FunctionalTest
{
    protected $usesDatabase = true;

    private Page $page;

    protected function setUp(): void
    {
        parent::setUp();

        $this->page = Page::create(['Title' => 'Toolbar smoke', 'URLSegment' => 'toolbar-smoke']);
        $this->page->write();
        $this->page->publishRecursive();
    }

    public function testToolbarRendersForAnAdmin(): void
    {
        $this->logInAsMemberWith('ADMIN');

        $this->assertToolbarPresent($this->get($this->page->Link()));
    }

    public function testToolbarRequiresTheBuiltAssets(): void
    {
        $this->logInAsMemberWith('ADMIN');

        $body = $this->assertToolbarPresent($this->get($this->page->Link()));

        $this->assertStringContainsString('client/dist/js/toolbar.js', $body);
        $this->assertStringContainsString('client/dist/css/toolbar.css', $body);
        $this->assertStringNotContainsString('client/dist/app.js', $body);
    }

    public function testToolbarLinksToTheCMSAndShowsItsVersion(): void
    {
        $this->logInAsMemberWith('ADMIN');
        $toolbar = AdminToolbar::create();

        $body = $this->assertToolbarPresent($this->get($this->page->Link()));

        $this->assertSame(AdminRootController::admin_url(), $toolbar->getAdminURL());
        $this->assertNotSame('', $toolbar->getCMSVersion());
        $this->assertStringContainsString(sprintf('href="%s"', $toolbar->getAdminURL()), $body);
        $this->assertStringContainsString($toolbar->getCMSVersion(), $body);
    }

    public function testMenusRenderOnceAtTheirPlacement(): void
    {
        $this->logInAsMemberWith('ADMIN');

        $body = $this->assertToolbarPresent($this->get($this->page->Link()));

        foreach (['PageMenu', 'CMSMenu', 'UserMenu'] as $menu) {
            $this->assertSame(1, substr_count($body, sprintf('<dialog id="%s"', $menu)), $menu);
        }

        // Start menus precede the buttons; end menus follow the toggles.
        $this->assertLessThan(strpos($body, 'data-flush-cache-button'), strpos($body, 'data-toggle-dialog="PageMenu"'));
        $this->assertLessThan(strpos($body, 'data-flush-cache-button'), strpos($body, 'data-toggle-dialog="CMSMenu"'));
        $this->assertGreaterThan(strpos($body, 'data-toggle-dialog="toggles"'), strpos($body, 'data-toggle-dialog="UserMenu"'));
    }

    public function testToolbarPermissionIsOfferedToAssign(): void
    {
        $this->assertArrayHasKey(AdminToolbar::PERMISSION, Permission::get_codes(false));
    }

    public function testToolbarIsAbsentForAnAnonymousVisitor(): void
    {
        $this->assertToolbarAbsent($this->get($this->page->Link()));
    }

    public function testToolbarIsAbsentForAMemberWithoutThePermission(): void
    {
        $this->logInAsMemberWith('CMS_ACCESS_CMSMain');

        $this->assertToolbarAbsent($this->get($this->page->Link()));
    }

    public function testToolbarRendersForAMemberWithOnlyTheToolbarPermission(): void
    {
        $this->logInAsMemberWith('ADMIN_TOOLBAR');

        $this->assertToolbarPresent($this->get($this->page->Link()));
    }

    public function testToolbarIsAbsentWhenTheMemberDisabledIt(): void
    {
        $member = Member::get()->byID($this->logInAsMemberWith('ADMIN'));
        $this->assertInstanceOf(Member::class, $member);
        $member->DisableAdminToolbar = true;
        $member->write();

        $this->assertToolbarAbsent($this->get($this->page->Link()));
    }

    public function testToolbarIsAbsentInCMSPreview(): void
    {
        $this->logInAsMemberWith('ADMIN');

        $this->assertToolbarAbsent($this->get($this->page->Link() . '?CMSPreview=1'));
    }

    public function testToolbarIsAbsentWhenDisabledByQueryString(): void
    {
        $this->logInAsMemberWith('ADMIN');

        $this->assertToolbarAbsent($this->get($this->page->Link() . '?AdminToolbarDisabled=1'));
    }

    public function testButtonsAndTogglesRender(): void
    {
        $this->logInAsMemberWith('ADMIN');

        $body = $this->assertToolbarPresent($this->get($this->page->Link()));

        foreach (['data-flush-cache-button', 'data-queries-button', 'data-timing-button', 'data-queries-toggle', 'data-timing-toggle'] as $hook) {
            $this->assertStringContainsString($hook, $body);
        }
    }

    public function testDisabledComponentIsNotRendered(): void
    {
        Config::modify()->set(FlushCacheButton::class, 'enabled', false);
        $this->logInAsMemberWith('ADMIN');

        $body = $this->assertToolbarPresent($this->get($this->page->Link()));

        $this->assertStringNotContainsString('data-flush-cache-button', $body);
    }

    public function testPageMenuRendersTheActionsAndToken(): void
    {
        $this->logInAsMemberWith('ADMIN');

        $body = $this->assertToolbarPresent($this->get($this->page->Link()));

        $this->assertStringContainsString('data-action="unpublish"', $body);
        $this->assertStringContainsString('data-action="unpublishAndArchive"', $body);
        $this->assertStringContainsString(sprintf('data-page-id="%d"', $this->page->ID), $body);
        $this->assertStringContainsString('data-page-actions', $body);
        $this->assertStringContainsString('data-endpoint=', $body);
        $this->assertStringContainsString('name="SecurityID"', $body);
        $this->assertStringContainsString('data-toggle-dialog="PageMenu"', $body);
    }

    public function testToolbarOnlyMemberSeesNoEditLinkOrCMSMenu(): void
    {
        $this->logInAsMemberWith('ADMIN_TOOLBAR');

        $body = $this->assertToolbarPresent($this->get($this->page->Link()));

        $this->assertStringNotContainsString('data-toggle-dialog="CMSMenu"', $body);
        $this->assertStringNotContainsString('font-icon-edit', $body);
        $this->assertStringContainsString('data-toggle-dialog="UserMenu"', $body);
    }

    private function logInAsMemberWith(string $permission): int
    {
        $memberId = $this->logInWithPermission($permission);
        $this->session()->set('loggedInAs', $memberId);

        return $memberId;
    }

    private function assertToolbarPresent(HTTPResponse $response): string
    {
        $body = (string) $response->getBody();

        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString('id="admin-toolbar"', $body);

        return $body;
    }

    private function assertToolbarAbsent(HTTPResponse $response): void
    {
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringNotContainsString('id="admin-toolbar"', (string) $response->getBody());
    }
}
