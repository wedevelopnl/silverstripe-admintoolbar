<?php

namespace WeDevelop\AdminToolbar\Extensions;

use SilverStripe\Control\Controller;
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
        $request = Controller::curr()?->getRequest();

        if ($request && $request->getVar('CMSPreview') === '1') {
            return null;
        }

        if ($request && $request->getVar('AdminToolbarDisabled') === '1') {
            return null;
        }

        if (!Permission::check('ADMIN_TOOLBAR')){
            return null;
        }

        /** @var Member|MemberExtension $currentUser */
        $currentUser = Security::getCurrentUser();

        if (!$currentUser->showAdminToolbar()){
            return null;
        }

        return Admintoolbar::create()->render();
    }
}
