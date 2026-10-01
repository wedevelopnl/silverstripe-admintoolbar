<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Tests\Fixture;

use SilverStripe\Dev\TestOnly;
use WeDevelop\AdminToolbar\Model\MenuItem;

class OtherFixtureMenuItem extends MenuItem implements TestOnly
{
    private static bool $enabled = false;

    private static string $menu = EndFixtureMenu::class;
}
