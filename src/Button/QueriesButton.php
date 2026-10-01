<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Button;

use WeDevelop\AdminToolbar\Model\Button;

class QueriesButton extends Button
{
    private static int $order = 20;

    private static string $title = 'Queries';

    private static string $icon = 'font-icon-menu-modaladmin';

    private static string $hook = 'data-queries-button';

    private static bool $hidden_until_enabled = true;
}
