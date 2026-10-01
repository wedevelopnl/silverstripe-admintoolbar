<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Model;

abstract class Toggle extends Component
{
    /** The `data-*` attribute the toggle's script binds to. */
    private static string $hook = '';

    public function getHook(): string
    {
        /** @var string $hook */
        $hook = static::config()->get('hook');

        return $hook;
    }
}
