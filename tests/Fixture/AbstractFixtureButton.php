<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Tests\Fixture;

use SilverStripe\Dev\TestOnly;
use WeDevelop\AdminToolbar\Model\Button;

abstract class AbstractFixtureButton extends Button implements TestOnly
{
    private static bool $enabled = false;
}
