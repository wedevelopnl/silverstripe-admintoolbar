<?php

namespace TheWebmen\AdminToolbar\Extensions;

use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Security\Member;
use SilverStripe\Security\Permission;

class MemberExtension extends DataExtension {

    private static $db = array(
        'DisableAdminToolbar' => 'Boolean',
        'AdminToolbarDefaultCollapsed' => 'Boolean'
    );

    public function updateCMSFields(FieldList $fields)
    {
        if(!Member::currentUser()->canHaveAdminToolbar()){
            $fields->removeByName('DisableAdminToolbar');
            $fields->removeByName('AdminToolbarDefaultCollapsed');
        }
    }

    public function canHaveAdminToolbar(){
        return Permission::check('ADMIN_TOOLBAR');
    }

    public function showAdminToolbar(){
        if($this->owner->DisableAdminToolbar){
            return false;
        }
        return true;
    }

}
