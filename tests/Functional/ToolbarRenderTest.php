<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Tests\Functional;

use Page;
use SilverStripe\Dev\FunctionalTest;

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
        $memberId = $this->logInWithPermission('ADMIN');
        $this->session()->set('loggedInAs', $memberId);

        $response = $this->get($this->page->Link());

        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString('id="admin-toolbar"', (string) $response->getBody());
    }

    public function testToolbarIsAbsentForAnAnonymousVisitor(): void
    {
        $response = $this->get($this->page->Link());

        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringNotContainsString('id="admin-toolbar"', (string) $response->getBody());
    }
}
