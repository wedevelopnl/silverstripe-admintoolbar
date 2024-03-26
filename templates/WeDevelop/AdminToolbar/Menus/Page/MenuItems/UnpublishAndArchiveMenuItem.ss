<% if $isMenuItemSupported %>
    <a href="#"
       class="ss-at-flex ss-at-items-center ss-at-font-medium $Link.ExtraClasses"
       data-pageid="$Link.PageId"
       data-action="$Link.Action">
        <% if $Icon %>
            <span class="ss-at-flex ss-at-items-center ss-at-mr-2 $Icon"></span>
        <% end_if %>
        $Title
    </a>
<% end_if %>
