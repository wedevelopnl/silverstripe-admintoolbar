<?php

namespace TheWebmen\AdminToolbar\Extensions;

use SilverStripe\Control\Controller;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Security\Member;
use SilverStripe\Security\Permission;
use SilverStripe\View\ViewableData;

class SiteTreeExtension extends DataExtension {

    public function AdminToolbar(){
        if(!Permission::check('ADMIN')){
            return false;
        }
        $viewer = ViewableData::create();
        return $viewer->customise(array(
            'CurrentPage' => Controller::curr(),
            'CurrentMember' => Member::currentUser()
        ))->renderWith('TheWebmen\\AdminToolbar\\AdminToolbar');
    }

}
