<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar;

use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Core\Config\Config;
use SilverStripe\Admin\AdminRootController;
use SilverStripe\Control\Director;
use SilverStripe\Security\Member;
use SilverStripe\Versioned\Versioned;

final class URLTranslator
{
    public static function getAdminURL(): string
    {
        $adminSegment = Config::forClass(AdminRootController::class)->get('url_base');

        return Director::absoluteBaseURL() . '/'. $adminSegment;
    }

    public static function getUserEditURL(Member $member): string
    {
        return self::getAdminURL() . "/security/users/EditForm/field/users/item/{$member->ID}/edit";
    }

    public static function getPageEditURL(SiteTree|int $siteTree): string
    {
        if (is_int($siteTree)) {
            $siteTree = Versioned::get_by_stage(SiteTree::class, 'Stage')->byID($pageId);
        }

        if (!$siteTree instanceof SiteTree) {
            return '';
        }

        return $siteTree->CMSEditLink();
    }
}
