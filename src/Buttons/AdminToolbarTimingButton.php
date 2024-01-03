<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Buttons;

use WeDevelop\AdminToolbar\Models\AdminToolbarButton;
use WeDevelop\AdminToolbar\Providers\AdminToolbarButtonProviderInterface;
use WeDevelop\AdminToolbar\Providers\AdminToolbarJavascriptProviderInterface;

class AdminToolbarTimingButton extends AdminToolbarButton implements AdminToolbarJavascriptProviderInterface, AdminToolbarButtonProviderInterface
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
        return 'font-icon-clock';
    }

    public function provideJavascript(): array
    {
        return [
            'wedevelopnl/silverstripe-admintoolbar:client/dist/timing-button.js',
        ];
    }

    public function getDataTags(): string
    {
        return 'data-timing-button';
    }

    public function provideAdminToolbarButton(): AdminToolbarButton
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
}
