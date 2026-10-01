<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Menu\Page;

use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Security\Member;

class UnpublishAndArchivePageItem extends PageActionItem
{
    private static int $order = 40;

    private static string $title = 'Unpublish and archive';

    private static string $icon = 'font-icon-trash';

    private static string $action = 'unpublishAndArchive';

    private static string $success_message = 'Page unpublished and archived';

    private static bool $destructive = true;

    public function appliesTo(SiteTree $page): bool
    {
        return $page->isPublished();
    }

    public function isAllowedFor(SiteTree $page, Member $member): bool
    {
        // @phpstan-ignore argument.type (versioned 3 documents $member as null; it takes the member to check)
        return $page->canUnpublish($member) && $page->canDelete($member);
    }

    public function perform(SiteTree $page): void
    {
        // doArchive() removes the record from both stages.
        $page->doArchive();
    }
}
