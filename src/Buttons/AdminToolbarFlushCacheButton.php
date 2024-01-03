<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Buttons;

use WeDevelop\AdminToolbar\Models\AdminToolbarButton;
use WeDevelop\AdminToolbar\Providers\AdminToolbarButtonProviderInterface;
use WeDevelop\AdminToolbar\Providers\AdminToolbarJavascriptProviderInterface;

class AdminToolbarFlushCacheButton extends AdminToolbarButton implements AdminToolbarJavascriptProviderInterface, AdminToolbarButtonProviderInterface
{
    public function getName(): string
    {
        return 'Flush Cache';
    }

    public function getHTML(): string
    {
        return 'Flush Cache';
    }

    public function getIcon(): string
    {
        return 'font-icon-back-in-time';
    }

    public function provideJavascript(): array
    {
        return [
            'wedevelopnl/silverstripe-admintoolbar:resources/js/flush-cache-button.js',
        ];
    }

    public function getDataTags(): string
    {
        return 'data-flush-cache-button';
    }

    public function provideAdminToolbarButton(): AdminToolbarButton
    {
        return self::create();
    }

    public function isButtonSupported(): bool
    {
        return true;
    }
}
