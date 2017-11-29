<?php

namespace TheWebmen\AdminToolbar\Extensions;

use SilverStripe\ORM\DataExtension;
use SilverStripe\Security\Member;
use SilverStripe\Security\Permission;
use TheWebmen\AdminToolbar\AdminToolbar;

class SiteTreeExtension extends DataExtension {

    public function AdminToolbar(){
        if(!Permission::check('ADMIN_TOOLBAR')){
            return false;
        }
        if(!Member::currentUser()->showAdminToolbar()){
            return false;
        }
        $toolbar = new AdminToolbar();
        return $toolbar->render();
    }



}
