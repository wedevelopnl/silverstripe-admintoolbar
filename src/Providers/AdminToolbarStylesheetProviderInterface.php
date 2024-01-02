<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Providers;

interface AdminToolbarStylesheetProviderInterface
{
    public function provideStylesheets(): array;
}
