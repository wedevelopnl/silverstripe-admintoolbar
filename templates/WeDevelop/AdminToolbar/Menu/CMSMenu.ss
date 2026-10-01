<div class="admin-toolbar-menu">
    <button type="button" class="ss-at-btn" data-toggle-dialog="$DialogId">
        <span class="ss-at-h-3.5 $Icon"></span>
        <span class="ss-at-btn-content max-lg:ss-at-hidden ss-at-ms-2">$Title</span>
    </button>
    <dialog id="$DialogId" class="ss-at-w-6/12 ss-at-bg-transparent p-0 backdrop:ss-at-bg-black backdrop:ss-at-bg-opacity-50">
        <div class="dialog-inner ss-at-relative ss-at-bg-white ss-at-p-6 ss-at-rounded-lg">
            <% include WeDevelop\AdminToolbar\Includes\DialogHeader Title=$SiteConfig.Title %>
            <ul class="ss-at-space-y-3">
                <% loop $MainMenu %>
                    <li>
                        <a href="$Link" class="ss-at-text-black ss-at-group ss-at-text-black hover:ss-at-text-primary ss-at-flex ss-at-items-center">
                            <span class="<% if $IconClass %>$IconClass<% else %>font-icon-database<% end_if %> ss-at-leading-none ss-at-black ss-at-opacity-25 group-hover:ss-at-text-primary group-hover:ss-at-opacity-100 ss-at-mr-3 ss-at-transition-all"></span>
                            <span>$Title</span>
                        </a>
                    </li>
                <% end_loop %>
            </ul>
        </div>
    </dialog>
</div>

