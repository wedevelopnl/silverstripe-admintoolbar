<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Providers;

use WeDevelop\AdminToolbar\Models\AdminToolbarMenu;

interface AdminToolbarMenuProviderInterface
{
    public function isMenuSupported(): bool;
    public function provideAdminToolbarMenu(): AdminToolbarMenu;
}
