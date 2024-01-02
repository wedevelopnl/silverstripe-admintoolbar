<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Providers;

interface AdminToolbarJavascriptProviderInterface
{
    public function provideJavascript(): array;
}
