<div class="admin-toolbar-menu">
    <button class="ss-at-btn" data-toggle-dialog="$Name">
        <span class="ss-at-h-3.5 ss-at-me-2 $Icon"></span>
        <span class="ss-at-btn-content">$Title</span>
    </button>
    <dialog id="$Name" class="ss-at-w-6/12 ss-at-bg-transparent p-0 backdrop:ss-at-bg-black backdrop:ss-at-bg-opacity-50">
        <div class="dialog-inner ss-at-relative ss-at-bg-white ss-at-p-6 ss-at-rounded-lg">
            <% include WeDevelop\AdminToolbar\Includes\DialogHeader Title='Elemental Grid' %>
            <% loop $Items %>
                <% if $Element.ClassName.ShortName != 'ElementRow' && $IsFirst %>
                    <div class="ss-at-grid ss-at-grid-cols-12 ss-at-p-4 ss-at-gap-4 ss-at-rounded-lg ss-at-border">
                        <div class="ss-at-col-span-{$Element.Size} ss-at-border ss-at-border-primary">$Element.ClassName.ShortName</div>
                <% else_if $Element.ClassName.ShortName == 'ElementRow' %>
                    <% if not $IsFirst %></div><% end_if %>
                    <div class="ss-at-grid ss-at-grid-cols-12 ss-at-gap-4 ss-at-p-4 ss-at-rounded-lg ss-at-border ss-at-mb-4 hover:ss-at-shadow-md ss-at-transition-all">
                        <div class="ss-at-col-span-12 ss-at-relative">
                            <div class="ss-at-flex ss-at-items-center">
                                <% include WeDevelop\AdminToolbar\Includes\GridElement %>
                            </div>
                        </div>
                    <% if $IsLast %></div><% end_if %>
                <% else %>
                    <div class="ss-at-col-span-{$Element.Size}">
                        <div class="ss-at-border ss-at-relative ss-at-p-4 ss-at-rounded-lg ss-at-flex ss-at-items-center ss-at-leading-tight ss-at-cursor-pointer hover:ss-at-shadow-md ss-at-transition-all">
                            <% include WeDevelop\AdminToolbar\Includes\GridElement %>
                        </div>
                    </div>
                <% end_if %>
            <% end_loop %>
        </div>
    </dialog>
</div>

