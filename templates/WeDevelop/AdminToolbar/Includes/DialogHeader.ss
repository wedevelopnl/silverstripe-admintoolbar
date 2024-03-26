<div class="ss-at-flex ss-at-mb-5 ss-at-items-center">
    <h3 class="ss-at-text-base ss-at-leading-tight ss-at-font-semibold">$Title</h3>
    <% if $Badge %>
        <span class="ss-at-ml-3 ss-at-font-medium ss-at-px-2 ss-at-py-1 ss-at-rounded-md ss-at-bg-$Badge.Color-200 ss-at-text-$Badge.Color-800 ss-at-text-3.5">
            $Badge.Label
        </span>
    <% end_if %>
    <div class="ss-at-ml-auto ss-at-text-5 ss-at-flex ss-at-items-center hover:ss-at-rotate-90 ss-at-origin-center ss-at-transition-all ss-at-cursor-pointer" data-toggle-dialog="$Name">
        <span class="font-icon-cross-mark"></span>
    </div>
</div>