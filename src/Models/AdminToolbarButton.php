<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Models;

use SilverStripe\Model\ModelData;
use SilverStripe\Core\Config\Configurable;

abstract class AdminToolbarButton extends ModelData implements AdminToolbarButtonInterface
{
    use Configurable;

    /** @config */
    private static int $order = 10;

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
        return static::config()->get('order') ?? self::$order;
    }

    public function getDataTags(): string
    {
        return '';
    }
}
