<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Providers;

use WeDevelop\AdminToolbar\Models\AdminToolbarToggle;

interface AdminToolbarToggleProviderInterface
{
    public function isToggleSupported(): bool;
    public function provideAdminToolbarToggle(): AdminToolbarToggle;
}
