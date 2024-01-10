<div id="admin-toolbar" class="ss-at-relative">
    <div class="admin-toolbar-inner ss-at-collapse ss-at-font-sans ss-at-text-4 ss-at-fixed ss-at-flex ss-at-z-10 ss-at-items-center ss-at-bottom-0 ss-at-left-0 ss-at-right-0 ss-at-py-2 ss-at-px-3 ss-at-bg-silverstripe-100 ss-at-border-t ss-at-border-silverstripe-300 ss-at-pr-12">
        <div class="ss-at-flex ss-at-items-center">
            <div class="ss-at-pr-3 ss-at-border-r ss-at-border-silverstripe-300 ss-at-mr-3">
                <a href="$AdminURL" target="_blank" class="cms-icon">
                    <i class="font-icon-silverstripe-cms"></i>
                    $CMSVersion
                </a>
            </div>
            <ul class="ss-at-flex ss-at-items-center ss-at-flex-wrap ss-at-space-x-2">
                <% loop $Menus %>
                    <li>
                        $Me
                    </li>
                <% end_loop %>
                <% loop $Buttons %>
                    <li>
                        $Me
                    </li>
                <% end_loop %>
            </ul>
        </div>
        <div class="ss-at-ml-auto ss-at-flex ss-at-items-center">
            <div>
                <button class="ss-at-btn" data-toggle-dialog="toggles">
                    Toggles
                </button>
                <dialog id="toggles">
                    <ul class="admin-toolbar-menu-items">
                        <% loop $Toggles %>
                            <li>$Me</li>
                        <% end_loop %>
                    </ul>
                    <button class="btn" data-toggle-dialog="toggles">
                        Sluiten
                    </button>
                </dialog>
            </div>
        </div>
    </div>
    <button class="ss-at-btn ss-at-fixed ss-at-right-3 ss-at-bottom-2 ss-at-z-20" data-toggle-admin-toolbar>
        <span class="font-icon-angle-right"></span>
    </button>
</div>