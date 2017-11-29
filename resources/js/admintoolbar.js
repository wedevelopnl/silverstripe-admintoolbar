document.addEventListener("DOMContentLoaded", function() {
    var adminToolbar = document.getElementById('admin-toolbar');
    if(!adminToolbar.classList.contains('admin-toolbar-collapsed')) {
        document.body.classList.add('has-admin-toolbar');
    }
    var adminToolbarToggle = document.getElementById('admin-toolbar-toggle');
    adminToolbarToggle.addEventListener('click', function() {
        if(adminToolbar.classList.contains('admin-toolbar-collapsed')){
            adminToolbar.classList.remove('admin-toolbar-collapsed');
            document.body.classList.add('has-admin-toolbar');
        }else{
            adminToolbar.classList.add('admin-toolbar-collapsed');
            document.body.classList.remove('has-admin-toolbar');
        }
    });
});
