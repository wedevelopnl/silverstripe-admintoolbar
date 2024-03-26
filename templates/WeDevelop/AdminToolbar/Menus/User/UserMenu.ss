<div class="admin-toolbar-menu">
    <div class="ss-at-flex ss-at-items-center">
        <button class="ss-at-btn ss-at-rounded-r-none" data-toggle-dialog="$Name">
            <span class="ss-at-h-3.5 $Icon"></span>
            <span class="ss-at-btn-content max-lg:ss-at-hidden ss-at-ms-2">$CurrentMember.Name</span>
        </button>
        <a href="$LogoutLink" class="ss-at-btn ss-at-bg-silverstripe ss-at-text-white ss-at-rounded-l-none">
            <span class="ss-at-h-3.5 font-icon-logout"></span>
        </a>
    </div>
    <dialog id="$Name" class="ss-at-w-3/12 ss-at-bg-transparent p-0 backdrop:ss-at-bg-black backdrop:ss-at-bg-opacity-50">
        <div class="dialog-inner ss-at-relative ss-at-bg-white ss-at-p-6 ss-at-rounded-lg">
            <% include WeDevelop\AdminToolbar\Includes\DialogHeader Title='User info' %>
            <ul class="ss-at-space-y-2">
                <% loop $Items %>
                    <li class="ss-at-inline-block">
                        $Me
                    </li>
                <% end_loop %>
            </ul>
        </div>
    </dialog>
</div>

