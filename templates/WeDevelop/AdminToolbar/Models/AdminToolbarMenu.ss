<div class="admin-toolbar-menu">
    <i class="icon $Icon"></i>
    <span><% if $HTML %>$HTML.HTML<% else %>$Name<% end_if %></span>
    <ul class="admin-toolbar-menu-items">
        <% loop $Items %>
            <li>$Me</li>
        <% end_loop %>
    </ul>
</div>

