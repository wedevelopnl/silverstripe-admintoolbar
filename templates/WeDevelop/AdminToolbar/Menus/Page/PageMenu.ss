<div class="admin-toolbar-menu">
    <div class="ss-at-flex ss-at-items-center ss-at-rounded-lg ss-at-bg-silverstripe">
        <a href="$EditMenuItem.Link.LinkURL" class="ss-at-btn ss-at-bg-transparent ss-at-text-white ss-at-rounded-r-none" target="_blank">
            <% if $Icon %>
                <span class="ss-at-flex ss-at-items-center ss-at-mr-2 font-icon-edit"></span>
            <% end_if %>
            Edit page
        </a>
        <button class="ss-at-btn ss-at-bg-white ss-at-bg-opacity-25 ss-at-text-white ss-at-rounded-l-none" data-toggle-dialog="$Name">
            <span class="ss-at-h-3.5 font-icon-info-circled"></span>
        </button>
    </div>
    <dialog id="$Name" class="ss-at-w-5/12 ss-at-bg-transparent p-0 backdrop:ss-at-bg-black backdrop:ss-at-bg-opacity-50">
        <div class="dialog-inner ss-at-relative ss-at-bg-white ss-at-p-6 ss-at-rounded-lg">
            <% include WeDevelop\AdminToolbar\Includes\DialogHeader Title=$CurrentPage.Title, Badge=$PublishState %>
            <ul class="ss-at-space-x-4 ss-at-flex ss-at-items-center ss-at-flex-wrap ss-at-opacity-65 ss-at-text-3.5 ss-at-mb-5">
                <li class="ss-at-relative after:content-[''] after:ss-at-w-0.5 after:ss-at-h-0.5 after:ss-at-absolute after:top-1/2 after:ss-at-bg-black after:ss-at-mx-2 after:ss-at-top-1/2 after:ss-at--translate-y-1/2">
                    <span><%t AdminToolbar.LAST_EDITED_ON 'Last edited on' %> $CurrentPage.LastEdited.Nice</span>
                </li>
                <li>
                    <a href="$AuthorEditLink" target="_blank"><%t AdminToolbar.LAST_EDITED_BY 'Last edited by' %> $Author.Name</a>
                </li>
            </ul>
            <ul class="ss-at-space-y-4 ss-at-leading-tight">
                <% loop $Items %>
                    <li>
                        $Me
                    </li>
                <% end_loop %>
            </ul>
        </div>
    </dialog>
</div>
