<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Menu;

use SilverStripe\Admin\LeftAndMain;
use SilverStripe\Model\ArrayData;
use SilverStripe\Model\List\SS_List;
use WeDevelop\AdminToolbar\Model\Menu;

class CMSMenu extends Menu
{
    private static int $order = 20;

    private static string $title = 'Menu';

    private static string $icon = 'font-icon-menu';

    public function isSupported(): bool
    {
        return $this->getMainMenu()->count() > 0;
    }

    /**
     * Filtered by the logged-in member, which is the context member on every real request.
     *
     * @return SS_List<ArrayData>
     */
    public function getMainMenu(): SS_List
    {
        return LeftAndMain::singleton()->MainMenu();
    }
}
