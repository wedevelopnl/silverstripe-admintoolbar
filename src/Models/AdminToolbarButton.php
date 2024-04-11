<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Models;

use SilverStripe\Core\Config\Configurable;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\View\ViewableData;

abstract class AdminToolbarButton extends ViewableData implements AdminToolbarButtonInterface
{
    use Configurable;

    /** @config */
    private static int $order = 10;

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
        return static::config()->get('order') ?? self::$order;
    }

    public function getDataTags(): string
    {
        return '';
    }
}
