<button type="button"
   class="ssat:flex ssat:items-center ssat:font-medium <% if $IsDestructive %>ssat:text-red-600 ssat:hover:text-red-700<% else %>ssat:text-black ssat:hover:text-primary<% end_if %>"
   data-page-id="$PageID"
   data-action="$Action">
    <span class="ssat:flex ssat:items-center ssat:mr-2 $Icon" aria-hidden="true"></span>
    $Title
</button>
