<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Tests\Fixture;

use SilverStripe\Dev\TestOnly;
use WeDevelop\AdminToolbar\Model\Button;

class FixtureButton extends Button implements TestOnly
{
    private static bool $enabled = false;

    private static int $order = 5;

    private static string $title = 'Fixture';

    private static string $icon = 'font-icon-edit';

    private static string $hook = 'data-fixture-button';

    /** @var list<string> */
    private static array $javascript = ['wedevelopnl/silverstripe-admintoolbar:client/dist/app.js'];

    /** @var list<string> */
    private static array $stylesheets = ['wedevelopnl/silverstripe-admintoolbar:client/dist/main.css'];
}
