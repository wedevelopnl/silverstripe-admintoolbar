<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Models;

use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\View\ViewableData;

abstract class AdminToolbarToggle extends ViewableData implements AdminToolbarToggleInterface
{
    public function getExtraClasses(): string
    {
        return '';
    }

    public function forTemplate(): DBHTMLText
    {
        return $this->renderWith(self::class);
    }

    public function getOrder(): int
    {
        return 0;
    }
}
