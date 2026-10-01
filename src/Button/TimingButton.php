<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Button;

use WeDevelop\AdminToolbar\Model\Button;

class TimingButton extends Button
{
    private static int $order = 30;

    private static string $title = 'Timing';

    private static string $icon = 'font-icon-clock';

    private static string $hook = 'data-timing-button';

    private static bool $hidden_until_enabled = true;

    /** @var list<string> */
    private static array $javascript = [
        'wedevelopnl/silverstripe-admintoolbar:client/dist/timing-button.js',
    ];
}
