<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Models;

use SilverStripe\Model\ModelData;

abstract class AdminToolbarToggle extends ModelData implements AdminToolbarToggleInterface
{
    public function getExtraClasses(): string
    {
        return '';
    }

    public function forTemplate(): string
    {
        return $this->renderWith(self::class)->forTemplate();
    }

    public function getOrder(): int
    {
        return 0;
    }
}
