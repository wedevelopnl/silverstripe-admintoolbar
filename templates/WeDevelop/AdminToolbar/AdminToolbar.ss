<div id="admin-toolbar" class="ss-at-relative ss-at-text-text">
    <div class="admin-toolbar-inner ss-at-collapse ss-at-font-sans ss-at-text-4 ss-at-fixed ss-at-flex ss-at-z-10 ss-at-items-center ss-at-bottom-0 ss-at-left-0 ss-at-right-0 ss-at-py-2 ss-at-px-3 ss-at-bg-silverstripe-100 ss-at-border-t ss-at-border-silverstripe-300 ss-at-pr-12">
        <div class="ss-at-flex ss-at-items-center">
            <div class="ss-at-pr-3 ss-at-border-r ss-at-border-silverstripe-300 ss-at-mr-3">
                <a href="$AdminURL" target="_blank" class="cms-icon ss-at-text-silverstripe hover:ss-at-text-primary ss-at-flex ss-at-items-center">
                    <i class="font-icon-silverstripe-cms ss-at-text-5 ss-at-mr-2"></i>
                    <div class="ss-at-px-1.5 ss-at-py-0.5 ss-at-bg-silverstripe ss-at-text-white ss-at-rounded-lg ss-at-font-semibold ss-at-text-3">$CMSVersion</div>
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
            <div class="ss-at-flex ss-at-items-center ss-at-space-x-1">
                <dialog id="toggles" class="ss-at-shadow-2xl ss-at-rounded-lg ss-at-peer ss-at-bg-transparent ss-at-mr-10 ss-at-mb-11 backdrop:ss-at-bg-transparent">
                    <div class="dialog-inner ss-at-relative ss-at-bg-silverstripe-100 ss-at-border ss-at-border-silverstripe-300 ss-at-p-3 ss-at-rounded-lg">
                        <ul class="admin-toolbar-menu-items ss-at-leading-none ss-at-space-y-2">
                            <% loop $Toggles %>
                                <li>$Me</li>
                            <% end_loop %>
                        </ul>
                    </div>
                </dialog>
                <button class="ss-at-btn peer-open:ss-at-bg-silverstripe peer-open:ss-at-text-white" data-toggle-dialog="toggles">
                    <span class="font-icon-dot-3"></span>
                </button>
                <% with $UserMenu %>
                    <% include WeDevelop\AdminToolbar\Menus\User\UserMenu %>
                <% end_with %>
            </div>
        </div>
    </div>
    <button class="ss-at-btn ss-at-fixed ss-at-right-3 ss-at-bottom-2 ss-at-z-20" data-toggle-admin-toolbar>
        <span class="font-icon-angle-right"></span>
    </button>
</div>