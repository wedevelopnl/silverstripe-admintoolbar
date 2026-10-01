<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Toggle;

use WeDevelop\AdminToolbar\Model\Toggle;

class TimingToggle extends Toggle
{
    private static int $order = 20;

    private static string $title = 'Timing';

    private static string $icon = 'font-icon-menu-clock';

    private static string $hook = 'data-timing-toggle';

    /** @var list<string> */
    private static array $javascript = [
        'wedevelopnl/silverstripe-admintoolbar:client/dist/timing-toggle.js',
    ];
}
