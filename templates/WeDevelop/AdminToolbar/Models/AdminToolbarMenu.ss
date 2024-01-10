<div class="admin-toolbar-menu">
    <button class="ss-at-btn" data-toggle-dialog="$Name">
        <span class="ss-at-h-3.5 ss-at-me-2 $Icon"></span>
        <span class="ss-at-btn-content">$Title</span>
    </button>
    <dialog id="$Name" class="ss-at-w-6/12 ss-at-bg-transparent">
        <div class="dialog-inner ss-at-relative ss-at-bg-white ss-at-p-6 ss-at-rounded-lg">
            <% include WeDevelop\AdminToolbar\Includes\DialogHeader Title=$Name %>
            <ul class="ss-at-space-y-1">
                <% loop $Items %>
                    <li>$Me</li>
                <% end_loop %>
            </ul>
        </div>
    </dialog>
</div>

