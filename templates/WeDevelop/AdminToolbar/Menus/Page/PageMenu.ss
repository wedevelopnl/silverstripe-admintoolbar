<div class="admin-toolbar-menu">
    <div class="ss-at-flex ss-at-items-center ss-at-rounded-lg ss-at-bg-silverstripe">
        <a href="$EditMenuItem.Link.LinkURL" class="ss-at-btn ss-at-bg-transparent ss-at-text-white ss-at-rounded-r-none" target="_blank">
            <% if $Icon %>
                <span class="ss-at-flex ss-at-items-center ss-at-mr-2 font-icon-edit"></span>
            <% end_if %>
            Edit page
        </a>
        <button class="ss-at-btn ss-at-bg-white ss-at-bg-opacity-25 ss-at-text-white ss-at-rounded-l-none" data-toggle-dialog="$Name">
            <span class="ss-at-h-3.5 font-icon-info-circled"></span>
        </button>
    </div>
    <dialog id="$Name" class="ss-at-w-5/12 ss-at-bg-transparent p-0 backdrop:ss-at-bg-black backdrop:ss-at-bg-opacity-50">
        <div class="dialog-inner ss-at-relative ss-at-bg-white ss-at-p-6 ss-at-rounded-lg">
            <% include WeDevelop\AdminToolbar\Includes\DialogHeader Title=$CurrentPage.Title, Badge=$PublishState %>
            <div id="responseMessage" class="response-message hidden" >
                <span class="ss-at-ml-3 ss-at-font-medium ss-at-px-2 ss-at-py-1 ss-at-rounded-md ss-at-bg-green-200 ss-at-text-greenr-800 ss-at-text-3.5">
            </span>
            </div>
            <ul class="ss-at-space-x-4 ss-at-flex ss-at-items-center ss-at-flex-wrap ss-at-opacity-65 ss-at-text-3.5 ss-at-mb-5">
                <li class="ss-at-relative after:content-[''] after:ss-at-w-0.5 after:ss-at-h-0.5 after:ss-at-absolute after:top-1/2 after:ss-at-bg-black after:ss-at-mx-2 after:ss-at-top-1/2 after:ss-at--translate-y-1/2">
                    <span><%t AdminToolbar.LAST_EDITED_ON 'Last edited on' %> $CurrentPage.LastEdited.Nice</span>
                </li>
                <li>
                    <a href="$AuthorEditLink" target="_blank"><%t AdminToolbar.LAST_EDITED_BY 'Last edited by' %> $Author.Name</a>
                </li>
            </ul>
            <ul class="ss-at-space-y-4 ss-at-leading-tight">
                <% loop $Items %>
                    <li>
                        $Me
                    </li>
                <% end_loop %>
                <input type="hidden" id="SecurityID" name="SecurityID" value="$SecurityID" />
            </ul>

        </div>
    </dialog>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const csrfToken = document.getElementById('SecurityID').value;
        const url = `/admintoolbaraction/pageAction`;
        document.querySelectorAll('a[data-action]').forEach(function(element) {
            element.addEventListener('click', function(event) {
                event.preventDefault();
                const pageId = this.getAttribute('data-pageid');
                const action = this.getAttribute('data-action');
                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': csrfToken
                    },
                    body: JSON.stringify({
                        page_id: pageId,
                        action: action
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        const responseMessageContainer = document.getElementById('responseMessage');
                        if (responseMessageContainer && data.message) {
                            const messageSpan = responseMessageContainer.querySelector('span');
                            if (messageSpan) {
                                messageSpan.textContent = data.message;
                                responseMessageContainer.classList.remove('hidden'); // Toon het message container
                            }
                        }
                    })
                    .catch((error) => {
                        console.error('Error:', error);
                        // Geef hier feedback over de fout aan de gebruiker
                    });
            });
        });
    });
</script>