<% if $Kind == 'row' %>
    <div data-grid-node="row" class="ssat:grid ssat:gap-3" style="grid-template-columns: repeat($Of, minmax(0, 1fr));">
        <% loop $Children %><% include WeDevelop\AdminToolbar\Integration\Grid\Includes\GridNode %><% end_loop %>
    </div>
<% else_if $Kind == 'column' %>
    <div data-grid-node="column" class="ssat:border ssat:border-silverstripe-300 ssat:rounded-md ssat:p-2" style="grid-column: span $Span / span $Span;">
        <% include WeDevelop\AdminToolbar\Integration\Grid\Includes\GridNodeLabel %>
        <% loop $Children %><% include WeDevelop\AdminToolbar\Integration\Grid\Includes\GridNode %><% end_loop %>
    </div>
<% else_if $Kind == 'element' %>
    <div data-grid-node="element" class="ssat:py-1">
        <% include WeDevelop\AdminToolbar\Integration\Grid\Includes\GridNodeLabel %>
    </div>
<% else %>
    <div data-grid-node="$Kind" class="ssat:border ssat:border-silverstripe-300 ssat:rounded-lg ssat:p-3 ssat:mb-3">
        <% include WeDevelop\AdminToolbar\Integration\Grid\Includes\GridNodeLabel %>
        <% loop $Children %><% include WeDevelop\AdminToolbar\Integration\Grid\Includes\GridNode %><% end_loop %>
    </div>
<% end_if %>
