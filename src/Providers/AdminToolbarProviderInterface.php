<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Providers;

interface AdminToolbarProviderInterface
{
    public function isSupported(): bool;
    public function provideAdminToolbarCSS(): ?string;
    public function provideAdminToolbarJS(): ?string;
    public function provideAdminToolbarHTML(): ?string;
}