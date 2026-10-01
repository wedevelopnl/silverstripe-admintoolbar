<div id="admin-toolbar" data-admin-toolbar class="ssat:max-md:hidden ssat:relative ssat:text-text">
    <div id="admin-toolbar-panel" data-toolbar-panel class="ssat:hidden ssat:font-sans ssat:text-base ssat:fixed ssat:flex ssat:z-10 ssat:items-center ssat:bottom-0 ssat:left-0 ssat:right-0 ssat:py-2 ssat:px-3 ssat:bg-silverstripe-100 ssat:border-t ssat:border-silverstripe-300 ssat:pr-12">
        <div class="ssat:flex ssat:items-center">
            <div class="ssat:pr-3 ssat:border-r ssat:border-silverstripe-300 ssat:mr-3">
                <a href="$AdminURL" target="_blank" class="cms-icon ssat:text-silverstripe ssat:hover:text-primary ssat:flex ssat:items-center">
                    <i class="font-icon-silverstripe-cms ssat:inline-flex ssat:items-center ssat:h-[1em] ssat:leading-none ssat:text-xl ssat:mr-2" aria-hidden="true"></i>
                    <div class="ssat:px-1.5 ssat:py-0.5 ssat:bg-silverstripe ssat:text-white ssat:rounded-lg ssat:font-semibold ssat:text-xs">$CMSVersion</div>
                </a>
            </div>
            <ul class="ssat:flex ssat:items-center ssat:flex-wrap ssat:space-x-2">
                <% loop $StartMenus %>
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
        <div class="ssat:ml-auto ssat:flex ssat:items-center">
            <div class="ssat:flex ssat:items-center ssat:space-x-1">
                <div class="ssat:relative ssat:z-10" data-dialog-anchor>
                    <dialog id="toggles" aria-label="<%t AdminToolbar.TOGGLES 'Toggles' %>" class="ssat:mb-5 ssat:-translate-y-full ssat:fixed ssat:bg-silverstripe-100 ssat:border ssat:border-solid ssat:border-silverstripe-300 ssat:p-3 ssat:rounded-lg">
                        <ul class="admin-toolbar-menu-items ssat:leading-none ssat:space-y-2">
                            <% loop $Toggles %>
                                <li>$Me</li>
                            <% end_loop %>
                        </ul>
                    </dialog>
                    <button type="button" class="ssat:btn ssat:peer-open:bg-silverstripe ssat:peer-open:text-white" data-toggle-dialog="toggles" aria-label="<%t AdminToolbar.TOGGLES 'Toggles' %>">
                        <span class="font-icon-dot-3 ssat:inline-flex ssat:items-center ssat:h-[1em] ssat:leading-none" aria-hidden="true"></span>
                    </button>
                </div>
                <% loop $EndMenus %>$Me<% end_loop %>
            </div>
        </div>
    </div>
    <button type="button" class="ssat:btn ssat:group ssat:fixed ssat:right-3 ssat:bottom-2 ssat:z-20" data-toggle-admin-toolbar aria-controls="admin-toolbar-panel" aria-expanded="false" aria-label="<%t AdminToolbar.COLLAPSE 'Show or hide the toolbar' %>">
        <span class="font-icon-angle-right ssat:inline-flex ssat:items-center ssat:h-[1em] ssat:leading-none ssat:origin-center ssat:group-aria-[expanded=false]:rotate-180" aria-hidden="true"></span>
    </button>
</div>
