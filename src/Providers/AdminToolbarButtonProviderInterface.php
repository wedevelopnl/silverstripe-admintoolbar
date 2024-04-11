<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Providers;

use WeDevelop\AdminToolbar\Models\AdminToolbarButton;

interface AdminToolbarButtonProviderInterface
{
    public function isButtonSupported(): bool;

    public function provideAdminToolbarButton(): AdminToolbarButton;
}
