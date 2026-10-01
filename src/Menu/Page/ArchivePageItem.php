<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Menu\Page;

use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Security\Member;

class ArchivePageItem extends PageActionItem
{
    private static int $order = 50;

    private static string $title = 'Archive';

    private static string $icon = 'font-icon-trash';

    private static string $action = 'archive';

    private static string $success_message = 'Page archived';

    private static bool $destructive = true;

    public function appliesTo(SiteTree $page): bool
    {
        return !$page->isPublished() && $page->isOnDraft();
    }

    public function isAllowedFor(SiteTree $page, Member $member): bool
    {
        return (bool) $page->canDelete($member);
    }

    public function perform(SiteTree $page): void
    {
        $page->doArchive();
    }
}
