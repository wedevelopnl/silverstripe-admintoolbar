<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Tests\Integration\Extension;

use SilverStripe\Dev\SapphireTest;
use SilverStripe\Security\Member;

final class MemberExtensionTest extends SapphireTest
{
    protected $usesDatabase = true;

    public function testFieldsShownForAMemberWithTheToolbarPermission(): void
    {
        $fields = $this->createMemberWithPermission('ADMIN_TOOLBAR')->getCMSFields();

        $this->assertNotNull($fields->dataFieldByName('DisableAdminToolbar'));
        $this->assertNotNull($fields->dataFieldByName('AdminToolbarDefaultCollapsed'));
    }

    public function testFieldsRemovedForAMemberWithoutIt(): void
    {
        $fields = $this->createMemberWithPermission('CMS_ACCESS_CMSMain')->getCMSFields();

        $this->assertNull($fields->dataFieldByName('DisableAdminToolbar'));
        $this->assertNull($fields->dataFieldByName('AdminToolbarDefaultCollapsed'));
    }

    public function testFieldsRemovedForAnUnsavedMember(): void
    {
        $fields = Member::create()->getCMSFields();

        $this->assertNull($fields->dataFieldByName('DisableAdminToolbar'));
        $this->assertNull($fields->dataFieldByName('AdminToolbarDefaultCollapsed'));
    }
}
