<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar;

use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Core\Config\Config;
use SilverStripe\Admin\AdminRootController;
use SilverStripe\Control\Director;
use SilverStripe\Security\Member;

final class URLTranslator
{
    public static function getAdminURL(): string
    {
        $adminSegment = Config::forClass(AdminRootController::class)->get('url_base');

        return Director::absoluteBaseURL() . $adminSegment;
    }

    public static function getUserEditURL(Member $member): string
    {
        return self::getAdminURL() . "/security/users/EditForm/field/users/item/{$member->ID}/edit";
    }

    public static function getPageEditURL(SiteTree|int $siteTreeOrID): string
    {
        if (!is_int($siteTreeOrID)) {
            $siteTreeOrID = $siteTreeOrID->ID;
        }

        return self::getAdminURL() . "/pages/edit/show/$siteTreeOrID/";
    }
}
