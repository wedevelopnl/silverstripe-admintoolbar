<a href="#"
   class="test ss-at-flex ss-at-items-center ss-at-font-medium $Link.ExtraClasses"
   data-pageid="$Link.PageId"
   data-action="$Link.Action">
    <% if $Icon %>
        <span class="ss-at-flex ss-at-items-center ss-at-mr-2 $Icon"></span>
    <% end_if %>
    $Title
</a>
<input type="hidden" id="SecurityID" name="SecurityID" value="$SecurityID" />

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const csrfToken = document.getElementById('SecurityID').value;
        const baseUrl = `/admintoolbaraction`;
        document.querySelectorAll('a[data-action]').forEach(function(element) {
            element.addEventListener('click', function(event) {
                event.preventDefault();
                const pageId = this.getAttribute('data-pageid');
                const action = this.getAttribute('data-action');
                const url = `${baseUrl}/${action}`;

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': csrfToken
                    },
                    body: JSON.stringify({
                        page_id: pageId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    // Verwerk de response
                    console.log('Success:', data);
                    // Geef hier feedback aan de gebruiker, bijv. door het tonen van een melding of het herladen van de pagina
                })
                .catch((error) => {
                    console.error('Error:', error);
                    // Geef hier feedback over de fout aan de gebruiker
                });
        });
    });
});
</script>
