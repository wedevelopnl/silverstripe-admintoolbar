<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Menus\ElementalGrid\MenuItems;

use DNADesign\Elemental\Models\BaseElement;
use DNADesign\Elemental\Models\ElementContent;
use SilverStripe\ORM\FieldType\DBHTMLText;
use WeDevelop\AdminToolbar\Menus\CMSMenu\CMSMenu;
use WeDevelop\AdminToolbar\Menus\ElementalGrid\ElementalGridMenu;
use WeDevelop\AdminToolbar\Models\AdminToolbarMenuItem;

class ElementalGridMenuItem extends AdminToolbarMenuItem
{
    private BaseElement $element;

    public function isMenuItemSupported(): bool
    {
        return true;
    }

    public function isForMenu(string $menuName): bool
    {
        return $menuName === ElementalGridMenu::MENU_NAME;
    }

    public function getOrder(): int
    {
        return 0;
    }

    public function setElement(BaseElement $element): self
    {
        $this->element = $element;

        return $this;
    }

    public function getElement(): BaseElement
    {
        return $this->element;
    }

    public function forTemplate(): DBHTMLText
    {
        return $this->renderWith(self::class);
    }
}
