<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Toggles;

use WeDevelop\AdminToolbar\Models\AdminToolbarToggle;
use WeDevelop\AdminToolbar\Providers\AdminToolbarJavascriptProviderInterface;
use WeDevelop\AdminToolbar\Providers\AdminToolbarToggleProviderInterface;

class AdminToolbarQueriesToggle extends AdminToolbarToggle implements AdminToolbarJavascriptProviderInterface, AdminToolbarToggleProviderInterface
{
    public function getName(): string
    {
        return 'Queries';
    }

    public function getHTML(): string
    {
        return 'Queries';
    }

    public function getIcon(): string
    {
        return 'font-icon-menu-modaladmin';
    }

    public function provideJavascript(): array
    {
        return [
            'wedevelopnl/silverstripe-admintoolbar:resources/js/queries-toggle.js',
        ];
    }

    public function getDataTags(): string
    {
        return 'data-queries-toggle';
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
