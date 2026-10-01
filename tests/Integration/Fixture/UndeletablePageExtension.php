<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Tests\Integration\Fixture;

use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Core\Extension;
use SilverStripe\Dev\TestOnly;

/**
 * A project rule that forbids deleting pages, even for administrators,
 * while leaving publishing rights alone.
 *
 * @extends Extension<SiteTree>
 */
class UndeletablePageExtension extends Extension implements TestOnly
{
    protected function extendCanDelete(): bool
    {
        return false;
    }
}
