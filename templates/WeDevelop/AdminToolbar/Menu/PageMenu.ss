<div>
    <div class="ssat:flex ssat:items-center ssat:rounded-lg ssat:bg-silverstripe">
        <% if $EditLink %>
        <a href="$EditLink" class="ssat:btn ssat:bg-transparent ssat:text-white ssat:rounded-r-none" target="_blank">
            <% if $Icon %>
                <span class="ssat:flex ssat:items-center font-icon-edit" aria-hidden="true"></span>
            <% end_if %>
            <span class="ssat:ms-2 ssat:max-lg:hidden"><%t AdminToolbar.EDIT_PAGE 'Edit page' %></span>
        </a>
        <% end_if %>
        <button type="button" class="ssat:btn ssat:bg-white/25 ssat:text-white ssat:rounded-l-none" data-toggle-dialog="$DialogId" aria-label="$Title">
            <span class="ssat:h-3.5 font-icon-info-circled" aria-hidden="true"></span>
        </button>
    </div>
    <dialog id="$DialogId" aria-labelledby="$DialogId-title" class="ssat:w-5/12 ssat:bg-transparent ssat:p-0 ssat:backdrop:bg-black/50">
        <div class="dialog-inner ssat:relative ssat:bg-white ssat:p-6 ssat:rounded-lg">
            <% include WeDevelop\AdminToolbar\Includes\DialogHeader Title=$Page.Title, Badge=$PublishBadge %>
            <ul class="ssat:space-x-4 ssat:flex ssat:items-center ssat:flex-wrap ssat:text-sm ssat:mb-5">
                <li class="ssat:opacity-65 ssat:relative ssat:after:content-[''] ssat:after:w-0.5 ssat:after:h-0.5 ssat:after:absolute ssat:after:top-1/2 ssat:after:bg-black ssat:after:mx-2 ssat:after:-translate-y-1/2">
                    <span><%t AdminToolbar.LAST_EDITED_ON 'Last edited on' %> $Page.LastEdited.Nice</span>
                </li>
                <% if $AuthorLink %>
                    <li>
                        <a href="$AuthorLink" target="_blank" class="ssat:hover:text-black ssat:text-primary"><%t AdminToolbar.LAST_EDITED_BY 'Last edited by' %> $AuthorName</a>
                    </li>
                <% end_if %>
            </ul>
            <div data-page-actions data-endpoint="$ActionEndpoint" data-error-message="<%t AdminToolbar.ACTION_FAILED 'The action could not be completed. Reload the page and try again.' %>">
                <input type="hidden" name="SecurityID" value="$SecurityID">
                <p data-action-message class="ssat:hidden ssat:font-medium ssat:px-2 ssat:py-1 ssat:rounded-md ssat:bg-red-100 ssat:text-red-800 ssat:text-sm" role="alert"></p>
                <ul class="ssat:space-y-4 ssat:leading-tight">
                    <% loop $Items %>
                        <li>
                            $Me
                        </li>
                    <% end_loop %>
                </ul>
            </div>
        </div>
    </dialog>
</div>
