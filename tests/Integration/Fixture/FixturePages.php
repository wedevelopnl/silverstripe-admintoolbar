<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Tests\Integration\Fixture;

use Page;
use SilverStripe\Versioned\Versioned;

/**
 * Puts the `pages.yml` records into their named states. YAML fixtures only
 * write the draft stage, so publishing happens here.
 */
trait FixturePages
{
    protected function publishFixturePages(): void
    {
        $this->fixturePage('published')->publishRecursive();

        $modified = $this->fixturePage('modified');
        $modified->publishRecursive();
        $modified->Title = 'Modified on draft';
        $modified->write();
    }

    /**
     * The page as it is now on the draft stage, so state checks see every write.
     */
    protected function fixturePage(string $identifier): Page
    {
        $page = Versioned::get_by_stage(Page::class, Versioned::DRAFT)->byID($this->idFromFixture(Page::class, $identifier));
        $this->assertInstanceOf(Page::class, $page);

        return $page;
    }

    protected function isOnStage(string $stage, int $pageId): bool
    {
        return Versioned::get_by_stage(Page::class, $stage)->byID($pageId) instanceof Page;
    }
}
