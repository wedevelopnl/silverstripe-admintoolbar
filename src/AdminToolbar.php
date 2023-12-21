<?php

namespace WeDevelop\AdminToolbar;

use SilverStripe\Control\Controller;
use SilverStripe\Core\ClassInfo;
use SilverStripe\Core\Config\Config;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\Security\PermissionProvider;
use SilverStripe\Security\Security;
use SilverStripe\Versioned\Versioned;
use SilverStripe\View\Requirements;
use SilverStripe\View\ViewableData;
use WeDevelop\AdminToolbar\Providers\AdminToolbarProviderInterface;

class AdminToolbar extends ViewableData implements PermissionProvider
{
    public function render(): DBHTMLText
    {
        Requirements::css('wedevelopnl/silverstripe-admintoolbar:resources/css/admintoolbar.css');
        Requirements::javascript('wedevelopnl/silverstripe-admintoolbar:resources/js/admintoolbar.js');
        $toolbarConfig = Config::inst()->get('AdminToolbar');
        $member = Security::getCurrentUser();
        $extraHTML = '';

        /** @var AdminToolbarProviderInterface $provider */
        foreach (ClassInfo::implementorsOf(AdminToolbarProviderInterface::class) as $provider) {
            if ($provider->isSupported()) {
                $css = $provider->provideAdminToolbarCSS();
                $js = $provider->provideAdminToolbarJS();
                $html = $provider->provideAdminToolbarHTML();

                if ($css) {
                    Requirements::css($css);
                }

                if ($js) {
                    Requirements::javascript($js);
                }

                if ($html) {
                    $extraHTML .= $html;
                }
            }
        }

        return $this->customise(array(
            'CurrentPage' => Controller::curr(),
            'CurrentMember' => $member,
            'ReadingMode' => Versioned::get_reading_mode(),
            'ShowCacheButton' => !isset($toolbarConfig['hide_cache_button']) || !$toolbarConfig['hide_cache_button'],
            'ShowStageButton' => !isset($toolbarConfig['hide_stage_button']) || !$toolbarConfig['hide_stage_button'],
            'ShowEditButton' => Controller::curr()->canEdit() && (!isset($toolbarConfig['hide_edit_button']) || !$toolbarConfig['hide_edit_button']),
            'StartCollapsed' => $member && $member->AdminToolbarDefaultCollapsed && $member->AdminToolbarDefaultCollapsed != '0',
            'ExtraHTML' => $extraHTML
        ))->renderWith(self::class);
    }

    public function providePermissions(): array
    {
        return [
            'ADMIN_TOOLBAR' => _t('AdminToolbar.USE_THE_ADMIN_TOOLBAR', 'Use the admin toolbar')
        ];
    }

}
