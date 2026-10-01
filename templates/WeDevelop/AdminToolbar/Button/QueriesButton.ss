<button type="button" $Hook data-summary="<%t AdminToolbar.QUERIES_SUMMARY '{ms} ms ({count} queries)' %>" class="ssat:btn ssat:btn-outline<% if $IsHiddenUntilEnabled %> ssat:hidden<% end_if %>">
    <span class="ssat:h-3.5 $Icon" aria-hidden="true"></span>
    <span class="ssat:ms-2" data-button-label>$Title</span>
</button>
