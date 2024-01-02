<div id="admin-toolbar"<% if $StartCollapsed %> class="admin-toolbar-collapsed"<% end_if %>>
    <div style="float:left;">
        <a href="$AdminURL" target="_blank" class="cms-icon">
            <i class="icon font-icon-silverstripe-cms"></i>
            $CMSVersion
        </a>
    </div>

    <% loop $Menus %>
        $Me
    <% end_loop %>

    <% loop $Buttons %>
        $Me
    <% end_loop %>

    <div class="admin-toolbar-menu admin-toolbar-toggle-menu">
        <i class="icon font-icon-dot-3"></i>
        <ul class="admin-toolbar-menu-items">
            <% loop $Toggles %>
                <li>$Me</li>
            <% end_loop %>
        </ul>
    </div>

    <span id="admin-toolbar-toggle">
        <span class="admin-toolbar-hidden-collapsed">&#x25ba;</span>
        <span class="admin-toolbar-visible-collapsed">&#x25c0;</span>
    </span>
</div>
