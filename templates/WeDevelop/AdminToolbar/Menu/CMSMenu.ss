<div>
    <button type="button" class="ssat:btn" data-toggle-dialog="$DialogId">
        <span class="ssat:h-3.5 $Icon" aria-hidden="true"></span>
        <span class="ssat:max-lg:hidden ssat:ms-2 ssat:me-1">$Title</span>
    </button>
    <dialog id="$DialogId" aria-labelledby="$DialogId-title" class="ssat:w-6/12 ssat:bg-transparent ssat:p-0 ssat:backdrop:bg-black/50">
        <div class="dialog-inner ssat:relative ssat:bg-white ssat:p-6 ssat:rounded-lg">
            <% include WeDevelop\AdminToolbar\Includes\DialogHeader Title=$SiteConfig.Title %>
            <ul class="ssat:space-y-3">
                <% loop $MainMenu %>
                    <li>
                        <a href="$Link" class="ssat:text-black ssat:group ssat:hover:text-primary ssat:flex ssat:items-center">
                            <span class="<% if $IconClass %>$IconClass<% else %>font-icon-database<% end_if %> ssat:inline-flex ssat:items-center ssat:h-[1em] ssat:leading-none ssat:opacity-25 ssat:group-hover:text-primary ssat:group-hover:opacity-100 ssat:mr-3 ssat:transition-all" aria-hidden="true"></span>
                            <span>$Title</span>
                        </a>
                    </li>
                <% end_loop %>
            </ul>
        </div>
    </dialog>
</div>
