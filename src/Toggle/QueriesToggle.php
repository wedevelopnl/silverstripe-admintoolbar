<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Toggle;

use WeDevelop\AdminToolbar\Model\Toggle;

class QueriesToggle extends Toggle
{
    private static int $order = 10;

    private static string $title = 'Queries';

    private static string $icon = 'font-icon-menu-modaladmin';

    private static string $hook = 'data-queries-toggle';

    /** @var list<string> */
    private static array $javascript = [
        'wedevelopnl/silverstripe-admintoolbar:client/dist/queries-toggle.js',
    ];
}
