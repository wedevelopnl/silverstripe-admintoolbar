<div class="admin-toolbar-menu">
    <button class="ss-at-btn" data-toggle-dialog="$Name">
        <span class="ss-at-h-3.5 ss-at-me-2 $Icon"></span>
        <span class="ss-at-btn-content">$Title</span>
    </button>
    <dialog id="$Name" class="ss-at-w-4/12 ss-at-bg-transparent p-0 backdrop:ss-at-bg-black backdrop:ss-at-bg-opacity-50">
        <div class="dialog-inner ss-at-relative ss-at-bg-white ss-at-p-6 ss-at-rounded-lg">
            <% include WeDevelop\AdminToolbar\Includes\DialogHeader Title=$CurrentPage.Title, Badge=$PublishState %>
            <ul class="ss-at-space-x-2 ss-at-flex ss-at-items-center ss-at-flex-wrap ss-at-opacity-65 ss-at-text-3.5 ss-at-mb-5">
                <li>
                    <span><%t AdminToolbar.LAST_EDITED_ON 'Last edited on' %> $CurrentPage.LastEdited.Nice</span>
                </li>
                <li>
                    <a href="$AuthorEditLink" target="_blank"><%t AdminToolbar.LAST_EDITED_BY 'Last edited by' %> $Author.Name</a>
                </li>
            </ul>
            <ul class="ss-at-space-y-4 ss-at-leading-tight">
                <% loop $Items %>
                    <li>
                        <a href="$Link.LinkURL" class="ss-at-flex ss-at-items-center ss-at-font-medium $Link.ExtraClasses" target="_blank">
                            <% if $Icon %>
                                <span class="ss-at-flex ss-at-items-center ss-at-mr-2 $Icon"></span>
                            <% end_if %>
                            $Title
                        </a>
                    </li>
                <% end_loop %>
            </ul>
        </div>
    </dialog>
</div>