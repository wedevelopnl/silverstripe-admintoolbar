<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Extension;

use SilverStripe\CMS\Controllers\ContentController;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Control\NullHTTPRequest;
use SilverStripe\Core\Extension;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\Security\Member;
use SilverStripe\Security\Permission;
use SilverStripe\Security\Security;
use WeDevelop\AdminToolbar\AdminToolbar;
use WeDevelop\AdminToolbar\ToolbarContext;

/**
 * Provides `$AdminToolbar` to page templates.
 *
 * @extends Extension<ContentController>
 */
class ContentControllerExtension extends Extension
{
    public function AdminToolbar(): ?DBHTMLText
    {
        $controller = $this->getOwner();
        $request = $controller->getRequest();

        if (
            $request instanceof NullHTTPRequest
            || $request->getVar('CMSPreview') === '1'
            || $request->getVar('AdminToolbarDisabled') === '1'
        ) {
            return null;
        }

        $member = Security::getCurrentUser();

        if (
            !$member instanceof Member
            || !Permission::checkMember($member, AdminToolbar::PERMISSION)
            || $member->DisableAdminToolbar
        ) {
            return null;
        }

        $page = $controller->data();

        return AdminToolbar::create()->render(
            new ToolbarContext($page instanceof SiteTree ? $page : null, $member, $request),
        );
    }
}
