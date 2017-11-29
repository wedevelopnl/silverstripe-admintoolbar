<?php

namespace TheWebmen\AdminToolbar;

use SilverStripe\Control\Controller;
use SilverStripe\Core\Config\Config;
use SilverStripe\Security\Member;
use SilverStripe\Security\PermissionProvider;
use SilverStripe\Security\Security;
use SilverStripe\Versioned\Versioned;
use SilverStripe\View\Requirements;
use SilverStripe\View\ViewableData;

class AdminToolbar extends ViewableData implements PermissionProvider {

    public function render(){
        Requirements::css('thewebmen/silverstripe-admintoolbar:resources/css/admintoolbar.css');
        Requirements::javascript('thewebmen/silverstripe-admintoolbar:resources/js/admintoolbar.js');
        $toolbarConfig = Config::inst()->get('AdminToolbar');
        $member = Security::getCurrentUser();
        $extraButtonsHTML = '';
        $this->extend('addExtraButtonsHTML', $extraButtonsHTML);
        return $this->customise(array(
            'CurrentPage' => Controller::curr(),
            'CurrentMember' => $member,
            'ReadingMode' => Versioned::get_reading_mode(),
            'ShowCacheButton' => !isset($toolbarConfig['hide_cache_button']) || !$toolbarConfig['hide_cache_button'],
            'ShowStageButton' => !isset($toolbarConfig['hide_stage_button']) || !$toolbarConfig['hide_stage_button'],
            'ShowEditButton' => Controller::curr()->canEdit() ? !isset($toolbarConfig['hide_edit_button']) || !$toolbarConfig['hide_edit_button'] : false,
            'StartCollapsed' => $member->AdminToolbarDefaultCollapsed && $member->AdminToolbarDefaultCollapsed != '0',
            'ExtraButtonsHTML' => $extraButtonsHTML
        ))->renderWith('TheWebmen\\AdminToolbar\\AdminToolbar');
    }

    public function providePermissions()
    {
        return [
            'ADMIN_TOOLBAR' => _t('AdminToolbar.USE_THE_ADMIN_TOOLBAR', 'Use the admin toolbar')
        ];
    }

}
