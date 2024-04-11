<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Extensions;

use SilverStripe\Control\Controller;
use SilverStripe\Control\NullHTTPRequest;
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
        if (
            !Controller::has_curr()
            || ($request = Controller::curr()->getRequest()) instanceof NullHTTPRequest
        ) {
            return null;
        }

        if (
            $request->getVar('CMSPreview') === '1'
            || $request->getVar('AdminToolbarDisabled') === '1'
            || !Permission::check('ADMIN_TOOLBAR')
        ) {
            return null;
        }

        /** @var Member|MemberExtension $currentUser */
        $currentUser = Security::getCurrentUser();

        if (!$currentUser->showAdminToolbar()) {
            return null;
        }

        return Admintoolbar::create()->render();
    }
}
