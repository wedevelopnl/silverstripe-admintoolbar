<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Menus\Page;

use SilverStripe\Control\Controller;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\Security\Member;
use SilverStripe\Versioned\Versioned;
use SilverStripe\View\ArrayData;
use WeDevelop\AdminToolbar\Menus\Page\MenuItems\EditMenuItem;
use WeDevelop\AdminToolbar\Menus\Page\MenuItems\LastEditedByMenuItem;
use WeDevelop\AdminToolbar\Menus\Page\MenuItems\LastEditedMenuItem;
use WeDevelop\AdminToolbar\Menus\Page\MenuItems\StageMenuItem;
use WeDevelop\AdminToolbar\Models\AdminToolbarMenu;
use WeDevelop\AdminToolbar\Providers\AdminToolbarMenuProviderInterface;
use WeDevelop\AdminToolbar\URLTranslator;

class PageMenu extends AdminToolbarMenu implements AdminToolbarMenuProviderInterface
{
    private int $order = 0;

    public const MENU_NAME = 'Page';

    public function getName(): string
    {
        return self::MENU_NAME;
    }

    public function getTitle(): ?string
    {
        return _t('AdminToolbar.PAGE', 'Page');
    }

    public function getHTML(): string
    {
        return '';
    }

    public function getIcon(): string
    {
        return 'font-icon-page-multiple';
    }

    public function provideAdminToolbarMenu(): AdminToolbarMenu
    {
        return self::create();
    }

    public function isMenuSupported(): bool
    {
        return true;
    }

    public function forTemplate(): DBHTMLText
    {
        return $this->renderWith(self::class);
    }

    public function getPublishState(): ArrayData
    {
        $version = $this->getPageVersion();

        $isPublished = $version?->isPublished() ?? false;
        $isArchived = $version?->isArchived() ?? false;
        $isDraft = $version?->isModifiedOnDraft() ?? false;

        $state = match (true) {
            $isPublished => ['Label' => 'Published', 'Color' => 'green'],
            $isArchived => ['Label' => 'Archived', 'Color' => 'yellow'],
            $isDraft => ['Label' => 'Draft', 'Color' => 'blue'],
            default => ['Label' => 'Unknown', 'Color' => 'orange'],
        };

        return ArrayData::create($state);
    }

    public function getAuthorEditLink(): ?string
    {
        $author = $this->getPageVersion()?->Author();

        if (!$author) {
            return null;
        }

        return URLTranslator::getUserEditURL($author);
    }

    public function getAuthorName(): string
    {
        return $this->getPageVersion()?->Author()->Name ?? _t('Author.UNKNOWN', 'Unknown author');
    }

    private function getPageVersion(): ?DataObject
    {
        $page = Controller::curr()->record;

        return Versioned::get_version($page['ClassName'], $page['ID'], $page['Version']);
    }

    public function getEditInfoItems(): ArrayList
    {
        $items = [];

        foreach ($this->getItems() as $item) {
            if ($item instanceof LastEditedByMenuItem || $item instanceof LastEditedMenuItem) {
                $items[] = $item;
            }
        }

        return ArrayList::create($items);
    }

    public function getEditMenuItem(): EditMenuItem
    {
        return EditMenuItem::singleton();
    }

    public function getOrder(): int
    {
        return $this->order;
    }
}
