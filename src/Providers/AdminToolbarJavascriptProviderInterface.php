<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Providers;

interface AdminToolbarJavascriptProviderInterface
{
    /**
     * @return array<string>
     */
    public function provideJavascript(): array;
}
