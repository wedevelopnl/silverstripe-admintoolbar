<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Button;

use WeDevelop\AdminToolbar\Model\Button;

class FlushCacheButton extends Button
{
    private static int $order = 10;

    private static string $title = 'Flush cache';

    private static string $icon = 'font-icon-back-in-time';

    private static string $hook = 'data-flush-cache-button';
}
