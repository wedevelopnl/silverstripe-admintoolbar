<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Tests\Integration\Integration\Grid;

use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Extension;
use SilverStripe\Dev\TestOnly;
use WeDevelop\Grid\Model\GridElement;

/**
 * A project rule hiding elements by title; inert until a test lists titles.
 *
 * @extends Extension<GridElement>
 */
class HiddenElementExtension extends Extension implements TestOnly
{
    /** @var list<string> */
    private static array $hidden_titles = [];

    protected function extendCanView(): ?bool
    {
        /** @var list<string> $hidden */
        $hidden = Config::inst()->get(self::class, 'hidden_titles');

        return in_array($this->getOwner()->Title, $hidden, true) ? false : null;
    }
}
