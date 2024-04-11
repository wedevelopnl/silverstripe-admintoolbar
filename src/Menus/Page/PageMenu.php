<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Menus\Page;

use SilverStripe\CMS\Controllers\ContentController;
use SilverStripe\Control\Controller;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\Versioned\Versioned;
use SilverStripe\View\ArrayData;
use WeDevelop\AdminToolbar\Menus\Page\MenuItems\EditMenuItem;
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

    private function getPageVersion(): ?\Page
    {
        if (!Controller::has_curr() || !($controller = Controller::curr()) instanceof ContentController) {
            return null;
        }

        if (!isset($controller->ClassName, $controller->ID, $controller->Version)) {
            return null;
        }

        /** @var \Page|null $versioned */
        $versioned = Versioned::get_version($controller->ClassName, $controller->ID, $controller->Version);
        return $versioned;
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
