<div id="admin-toolbar"<% if $StartCollapsed %> class="admin-toolbar-collapsed"<% end_if %>>
    <div>
        <a href="/admin/security/EditForm/field/Members/item/{$CurrentMember.ID}/edit"><%t AdminToolbar.LOGGED_IN_AS "Logged in as" %>
            : $CurrentMember.Name</a>
        <a href="/Security/Logout"><%t AdminToolbar.LOGOUT "Logout" %></a>
        <a href="/admin"><%t AdminToolbar.ADMIN "Admin" %></a>

        <% if $ShowEditButton %>
            <a href="$CurrentPage.CMSEditLink"><%t AdminToolbar.EDIT "Edit" %> "$CurrentPage.Title"</a>
        <% end_if %>

        <% if $ShowCacheButton %>
            <a href="$CurrentPage.Link?flush=all"><%t AdminToolbar.CLEAR_CACHE "Clear cache" %></a>
        <% end_if %>

        <% if $ShowStageButton %>
            <% if $ReadingMode == 'Stage.Stage' %>
                <a href="$CurrentPage.Link?stage=Live"><%t AdminToolbar.SWITCH_TO_LIVE "Switch to Live" %></a>
            <% else %>
                <a href="$CurrentPage.Link?stage=Stage"><%t AdminToolbar.SWITCH_TO_STAGE "Switch to Stage" %></a>
            <% end_if %>
        <% end_if %>
        $ExtraButtonsHTML
        <span><%t AdminToolbar.LAST_EDITED_ON "Last edited on" %>: $CurrentPage.LastEdited</span>
    </div>
    <span id="admin-toolbar-toggle">
        <span class="admin-toolbar-hidden-collapsed">&#x25ba;</span>
        <span class="admin-toolbar-visible-collapsed">&#x25c0;</span>
    </span>
</div>
