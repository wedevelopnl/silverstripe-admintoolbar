<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Model;

abstract class Button extends Component
{
    /** The `data-*` attribute the button's script binds to. */
    private static string $hook = '';

    /** Rendered hidden; its script reveals it once the matching toggle is on. */
    private static bool $hidden_until_enabled = false;

    public function getHook(): string
    {
        /** @var string $hook */
        $hook = static::config()->get('hook');

        return $hook;
    }

    public function isHiddenUntilEnabled(): bool
    {
        /** @var bool $hidden */
        $hidden = static::config()->get('hidden_until_enabled');

        return $hidden;
    }
}
