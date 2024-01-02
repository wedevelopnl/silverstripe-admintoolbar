<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Buttons;

use WeDevelop\AdminToolbar\Models\AdminToolbarButton;
use WeDevelop\AdminToolbar\Providers\AdminToolbarButtonProviderInterface;
use WeDevelop\AdminToolbar\Providers\AdminToolbarJavascriptProviderInterface;
use WeDevelop\AdminToolbar\Providers\AdminToolbarStylesheetProviderInterface;

class AdminToolbarQueriesButton extends AdminToolbarButton implements AdminToolbarJavascriptProviderInterface, AdminToolbarButtonProviderInterface, AdminToolbarStylesheetProviderInterface
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
            'wedevelopnl/silverstripe-admintoolbar:resources/js/queries-button.js',
        ];
    }

    public function getDataTags(): string
    {
        return 'data-queries-button';
    }

    public function provideAdminToolbarButton(): ?AdminToolbarButton
    {
        return self::create();
    }

    public function isButtonSupported(): bool
    {
        return true;
    }

    public function getExtraClasses(): string
    {
        return 'admin-toolbar-hidden';
    }

    public function provideStylesheets(): array
    {
        return [
            'wedevelopnl/silverstripe-admintoolbar:resources/css/queries-button.css',
        ];
    }
}
