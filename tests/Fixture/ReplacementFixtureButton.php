<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Tests\Fixture;

use SilverStripe\Dev\TestOnly;

class ReplacementFixtureButton extends FixtureButton implements TestOnly
{
    private static bool $enabled = false;
}
