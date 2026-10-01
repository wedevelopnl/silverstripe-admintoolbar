<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Model;

use SilverStripe\Core\ClassInfo;
use SilverStripe\Model\List\ArrayList;

abstract class Menu extends Component
{
    public const string PLACEMENT_START = 'start';

    public const string PLACEMENT_END = 'end';

    /** `start` renders left of the buttons, `end` right of the toggles. */
    private static string $placement = self::PLACEMENT_START;

    public function getPlacement(): string
    {
        /** @var string $placement */
        $placement = static::config()->get('placement');

        return $placement;
    }

    /** Dialog element id; also the `data-toggle-dialog` value that opens it. */
    public function getDialogId(): string
    {
        return ClassInfo::shortName(static::class);
    }

    /**
     * Items whose `menu` config names this menu's class or one of its parents,
     * so a project subclass of a built-in menu keeps the built-in items.
     *
     * @return ArrayList<MenuItem>
     */
    public function getItems(): ArrayList
    {
        $items = array_filter(
            Component::discover(MenuItem::class, $this->getContext()),
            fn (MenuItem $item): bool => $item->getMenu() !== '' && is_a($this, $item->getMenu()),
        );

        return ArrayList::create(array_values($items));
    }
}
