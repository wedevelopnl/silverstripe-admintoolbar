<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Providers;

use WeDevelop\AdminToolbar\Models\AdminToolbarMenuItem;

interface AdminToolbarMenuItemProviderInterface
{
    public function isForMenu(string $menuName): bool;

    public function isMenuItemSupported(): bool;

    public function provideAdminToolbarMenuItem(): AdminToolbarMenuItem;
}
