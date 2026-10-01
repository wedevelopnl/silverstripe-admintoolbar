<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Menu\Page;

use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Security\Member;

class UnpublishPageItem extends PageActionItem
{
    private static int $order = 30;

    private static string $title = 'Unpublish';

    private static string $icon = 'font-icon-eye-with-line';

    private static string $action = 'unpublish';

    private static string $success_message = 'Page unpublished';

    private static bool $destructive = false;

    public function appliesTo(SiteTree $page): bool
    {
        return $page->isPublished();
    }

    public function isAllowedFor(SiteTree $page, Member $member): bool
    {
        // @phpstan-ignore argument.type (versioned 3 documents $member as null; it takes the member to check)
        return (bool) $page->canUnpublish($member);
    }

    public function perform(SiteTree $page): void
    {
        $page->doUnpublish();
    }
}
