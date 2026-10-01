<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Tests\Fixture;

use SilverStripe\Dev\TestOnly;
use WeDevelop\AdminToolbar\Model\Button;

class SameOrderFixtureButton extends Button implements TestOnly
{
    private static bool $enabled = false;

    private static int $order = 5;
}
