<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Toggles;

use WeDevelop\AdminToolbar\Models\AdminToolbarToggle;
use WeDevelop\AdminToolbar\Providers\AdminToolbarJavascriptProviderInterface;
use WeDevelop\AdminToolbar\Providers\AdminToolbarToggleProviderInterface;

class AdminToolbarTimingToggle extends AdminToolbarToggle implements AdminToolbarJavascriptProviderInterface, AdminToolbarToggleProviderInterface
{
    public function getName(): string
    {
        return 'Timing';
    }

    public function getHTML(): string
    {
        return 'Timing';
    }

    public function getIcon(): string
    {
        return 'font-icon-menu-clock';
    }

    public function provideJavascript(): array
    {
        return [
            'wedevelopnl/silverstripe-admintoolbar:resources/js/timing-toggle.js',
        ];
    }

    public function getDataTags(): string
    {
        return 'data-timing-toggle';
    }

    public function provideAdminToolbarToggle(): ?AdminToolbarToggle
    {
        return self::create();
    }

    public function isToggleSupported(): bool
    {
        return true;
    }
}
