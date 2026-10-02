<div>
    <div class="ssat:flex ssat:items-center">
        <button type="button" class="ssat:btn ssat:rounded-r-none" data-toggle-dialog="$DialogId">
            <span class="ssat:h-3.5 $Icon" aria-hidden="true"></span>
            <span class="ssat:max-lg:hidden ssat:ms-2 ssat:me-1">$Member.Name</span>
        </button>
        <a href="$LogoutLink" aria-label="<%t AdminToolbar.LOGOUT 'Log out' %>" class="ssat:btn ssat:bg-silverstripe ssat:text-white ssat:rounded-l-none">
            <span class="ssat:h-3.5 font-icon-logout" aria-hidden="true"></span>
        </a>
    </div>
    <dialog id="$DialogId" aria-labelledby="$DialogId-title" class="ssat:w-3/12 ssat:bg-transparent ssat:p-0 ssat:backdrop:bg-black/50">
        <div class="dialog-inner ssat:relative ssat:bg-white ssat:p-6 ssat:rounded-lg">
            <% include WeDevelop\AdminToolbar\Includes\DialogHeader Title=$Title %>
            <ul class="ssat:space-y-2">
                <% loop $Items %>
                    <li class="ssat:inline-block">
                        $Me
                    </li>
                <% end_loop %>
            </ul>
        </div>
    </dialog>
</div>
