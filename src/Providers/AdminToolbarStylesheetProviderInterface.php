<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Providers;

interface AdminToolbarStylesheetProviderInterface
{
    /**
     * @return array<string>
     */
    public function provideStylesheets(): array;
}
