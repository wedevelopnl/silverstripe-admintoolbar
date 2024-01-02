<?php

namespace WeDevelop\AdminToolbar;

use SilverStripe\Admin\LeftAndMain;
use SilverStripe\Control\Controller;
use SilverStripe\Core\ClassInfo;
use SilverStripe\Core\Config\Config;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\Security\PermissionProvider;
use SilverStripe\Security\Security;
use SilverStripe\Versioned\Versioned;
use SilverStripe\View\Requirements;
use SilverStripe\View\ViewableData;
use WeDevelop\AdminToolbar\Models\AdminToolbarButtonInterface;
use WeDevelop\AdminToolbar\Models\AdminToolbarMenuInterface;
use WeDevelop\AdminToolbar\Models\AdminToolbarToggleInterface;
use WeDevelop\AdminToolbar\Providers\AdminToolbarButtonProviderInterface;
use WeDevelop\AdminToolbar\Providers\AdminToolbarMenuProviderInterface;
use WeDevelop\AdminToolbar\Providers\AdminToolbarProviderInterface;
use WeDevelop\AdminToolbar\Providers\AdminToolbarJavascriptProviderInterface;
use WeDevelop\AdminToolbar\Providers\AdminToolbarStylesheetProviderInterface;
use WeDevelop\AdminToolbar\Providers\AdminToolbarToggleProviderInterface;

class AdminToolbar extends ViewableData implements PermissionProvider
{
    /** @config */
    private static array $disabled_menus = [];

    public function render(): DBHTMLText
    {
        Requirements::css('wedevelopnl/silverstripe-admintoolbar:resources/css/admintoolbar.css');
        Requirements::javascript('wedevelopnl/silverstripe-admintoolbar:resources/js/admintoolbar.js');

        $toolbarConfig = Config::inst()->get(self::class);
        $member = Security::getCurrentUser();

        return $this->customise([
            'Menus' => ArrayList::create($this->getMenus()),
            'Toggles' => ArrayList::create($this->getToggles()),
            'Buttons' => ArrayList::create($this->getButtons()),
            'CurrentPage' => Controller::curr(),
            'CurrentMember' => $member,
            'ReadingMode' => Versioned::get_reading_mode(),
            'ShowCacheButton' => !isset($toolbarConfig['hide_cache_button']) || !$toolbarConfig['hide_cache_button'],
            'ShowStageButton' => !isset($toolbarConfig['hide_stage_button']) || !$toolbarConfig['hide_stage_button'],
            'ShowEditButton' => Controller::curr()->canEdit() && (!isset($toolbarConfig['hide_edit_button']) || !$toolbarConfig['hide_edit_button']),
            'StartCollapsed' => $member && $member->AdminToolbarDefaultCollapsed && $member->AdminToolbarDefaultCollapsed != '0',
        ])->renderWith(self::class);
    }

    public function getMenus(): array
    {
        $menus = [];

        foreach (ClassInfo::implementorsOf(AdminToolbarMenuProviderInterface::class) as $menu) {
            /** @var AdminToolbarMenuProviderInterface $inst */
            $inst = $menu::create();

            if ($inst->isMenuSupported()) {
                $menu = $inst->provideAdminToolbarMenu();

                if (!in_array($menu->getName(), self::config()->get('disabled_menus') ?? [])) {
                    $this->provideJSandCSS($menu);
                    $menus[] = $menu;
                }
            }
        }

        usort($menus, static function(
            AdminToolbarMenuInterface $menuA,
            AdminToolbarMenuInterface $menuB,
        ) {
            return $menuA->getOrder() <=> $menuB->getOrder();
        });

        return $menus;
    }

    public function getToggles(): array
    {
        $toggles = [];

        foreach (ClassInfo::implementorsOf(AdminToolbarToggleProviderInterface::class) as $toggle) {
            /** @var AdminToolbarToggleProviderInterface $inst */
            $inst = $toggle::create();

            if ($inst->isToggleSupported()) {
                $toggle = $inst->provideAdminToolbarToggle();

                if (!in_array($toggle->getName(), self::config()->get('disabled_toggles') ?? [])) {
                    $this->provideJSandCSS($toggle);
                    $toggles[] = $toggle;
                }
            }
        }

        usort($toggles, static function(
            AdminToolbarToggleInterface $toggleA,
            AdminToolbarToggleInterface $toggleB,
        ) {
            return $toggleA->getOrder() <=> $toggleB->getOrder();
        });

        return $toggles;
    }

    public function getButtons(): array
    {
        $buttons = [];

        foreach (ClassInfo::implementorsOf(AdminToolbarButtonProviderInterface::class) as $button) {
            /** @var AdminToolbarButtonProviderInterface $inst */
            $inst = $button::create();

            if ($inst->isButtonSupported()) {
                $button = $inst->provideAdminToolbarButton();

                if (!in_array($button->getName(), self::config()->get('disabled_buttons') ?? [])) {
                    $this->provideJSandCSS($button);
                    $buttons[] = $button;
                }
            }
        }

        usort($buttons, static function(
            AdminToolbarButtonInterface $buttonA,
            AdminToolbarButtonInterface $buttonB,
        ) {
            return $buttonA->getOrder() <=> $buttonB->getOrder();
        });

        return $buttons;
    }

    public function getAdminURL(): string
    {
        return URLTranslator::getAdminURL();
    }

    public function getCMSVersion(): string
    {
       return LeftAndMain::create()->CMSVersionNumber();
    }

    public function providePermissions(): array
    {
        return [
            'ADMIN_TOOLBAR' => _t('AdminToolbar.USE_THE_ADMIN_TOOLBAR', 'Use the admin toolbar')
        ];
    }

    private function provideJSandCSS(object $inst): void
    {
        if ($inst instanceof AdminToolbarJavascriptProviderInterface) {
            $scripts = $inst->provideJavascript();

            foreach ($scripts as $script) {
                Requirements::javascript($script);
            }
        }

        if ($inst instanceof AdminToolbarStylesheetProviderInterface) {
            $sheets = $inst->provideStylesheets();

            foreach ($sheets as $sheet) {
                Requirements::css($sheet);
            }
        }
    }
}
