<?php

namespace WeDevelop\AdminToolbar\Extensions;

use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\Security\Member;
use SilverStripe\Security\Permission;
use SilverStripe\Security\Security;
use WeDevelop\AdminToolbar\AdminToolbar;

class SiteTreeExtension extends DataExtension
{
    public function AdminToolbar(): ?DBHTMLText
    {
        if (!Permission::check('ADMIN_TOOLBAR')){
            return null;
        }

        /** @var Member|MemberExtension $currentUser */
        $currentUser = Security::getCurrentUser();

        if (!$currentUser->showAdminToolbar()){
            return null;
        }

        return (new AdminToolbar())->render();
    }
}
