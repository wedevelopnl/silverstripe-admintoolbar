<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar;

use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Security\Member;

/**
 * What the toolbar is being rendered for. Built once per request by
 * {@see Extension\ContentControllerExtension} and handed to every component.
 */
final readonly class ToolbarContext
{
    public function __construct(
        public ?SiteTree $page,
        public Member $member,
        public HTTPRequest $request,
    ) {
    }
}
