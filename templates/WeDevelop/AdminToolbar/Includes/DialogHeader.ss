<div class="ssat:flex ssat:mb-5 ssat:items-center">
    <h3 id="$DialogId-title" class="ssat:text-lg ssat:leading-tight ssat:font-semibold">$Title</h3>
    <% if $Badge %>
        <span class="ssat:ml-3 ssat:font-medium ssat:px-2 ssat:py-1 ssat:rounded-md $Badge.Classes ssat:text-sm">
            $Badge.Label
        </span>
    <% end_if %>
    <button type="button" class="ssat:ml-auto ssat:text-xl ssat:rounded-md ssat:flex ssat:items-center ssat:hover:rotate-90 ssat:origin-center ssat:transition-all ssat:cursor-pointer" data-toggle-dialog="$DialogId" aria-label="<%t AdminToolbar.CLOSE 'Close' %>">
        <span class="font-icon-cross-mark ssat:inline-flex ssat:items-center ssat:h-[1em] ssat:leading-none" aria-hidden="true"></span>
    </button>
</div>
