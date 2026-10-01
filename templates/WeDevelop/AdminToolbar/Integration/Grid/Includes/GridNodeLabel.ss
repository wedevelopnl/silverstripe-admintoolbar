<div class="ssat:flex ssat:items-center ssat:gap-2 ssat:mb-2 ssat:text-sm">
    <span class="$Icon ssat:inline-flex ssat:items-center ssat:h-[1em] ssat:leading-none ssat:opacity-50" aria-hidden="true"></span>
    <% if $Link %>
        <a href="$Link" target="_blank" rel="noopener" class="ssat:text-black ssat:hover:text-primary ssat:underline">$Title</a>
    <% else %>
        <span>$Title</span>
    <% end_if %>
</div>
