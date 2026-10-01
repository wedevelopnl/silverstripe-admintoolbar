<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Menu;

use SilverStripe\Admin\SecurityAdmin;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use SilverStripe\Model\ArrayData;
use SilverStripe\Security\Member;
use SilverStripe\Versioned\Versioned;
use WeDevelop\AdminToolbar\Menu\Page\PublishState;
use WeDevelop\AdminToolbar\Model\Menu;

class PageMenu extends Menu
{
    private static int $order = 10;

    private static string $title = 'Page';

    private static string $icon = 'font-icon-page-multiple';

    public function isSupported(): bool
    {
        return $this->getContext()->page instanceof SiteTree;
    }

    public function getPage(): ?SiteTree
    {
        return $this->getContext()->page;
    }

    public function getEditLink(): ?string
    {
        $page = $this->getPage();

        return $page?->canEdit($this->getContext()->member) ? $page->getCMSEditLink() : null;
    }

    public function getPublishBadge(): ArrayData
    {
        $page = $this->getPage();
        $state = PublishState::of(
            (bool) $page?->isPublished(),
            (bool) $page?->isModifiedOnDraft(),
            (bool) $page?->isArchived(),
        );

        return ArrayData::create(['Label' => $state->getLabel(), 'Classes' => $state->getBadgeClasses()]);
    }

    public function getActionEndpoint(): string
    {
        return Controller::join_links(Director::baseURL(), 'admintoolbaraction', 'pageAction');
    }

    public function getAuthorName(): string
    {
        return $this->getAuthor()?->getName() ?? _t(self::class . '.UNKNOWN_AUTHOR', 'Unknown author');
    }

    public function getAuthorLink(): ?string
    {
        $author = $this->getAuthor();
        $securityAdmin = SecurityAdmin::singleton();

        return $author instanceof Member && $securityAdmin->canView($this->getContext()->member)
            ? $securityAdmin->getCMSEditLinkForManagedDataObject($author)
            : null;
    }

    /** Only version records carry `AuthorID`, so the author is read from the version being viewed. */
    private function getAuthor(): ?Member
    {
        $page = $this->getPage();

        if (!$page instanceof SiteTree) {
            return null;
        }

        // get_version() is documented non-null but returns null when the version row is gone.
        /** @var SiteTree|null $version */
        $version = Versioned::get_version($page::class, $page->ID, $page->Version);
        $author = $version?->Author();

        return $author instanceof Member ? $author : null;
    }
}
