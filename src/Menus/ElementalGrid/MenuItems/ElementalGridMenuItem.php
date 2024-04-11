<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Menus\ElementalGrid\MenuItems;

use SilverStripe\ORM\FieldType\DBHTMLText;
use WeDevelop\AdminToolbar\Menus\ElementalGrid\ElementalGridMenu;
use WeDevelop\AdminToolbar\Models\AdminToolbarMenuItem;

class ElementalGridMenuItem extends AdminToolbarMenuItem
{
    private \DNADesign\Elemental\Models\BaseElement $element;

    public function getName(): string
    {
        return 'ElementalGrid';
    }

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

    public function setElement(\DNADesign\Elemental\Models\BaseElement $element): self
    {
        $this->element = $element;

        return $this;
    }

    public function getElement(): \DNADesign\Elemental\Models\BaseElement
    {
        return $this->element;
    }

    public function forTemplate(): DBHTMLText
    {
        return $this->renderWith(self::class);
    }
}
