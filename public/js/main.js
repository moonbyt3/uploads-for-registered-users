jQuery(document).ready(function ($) {
    // Open files in new tab
    var openButtons = document.querySelectorAll('[js-ufru-open-file]');

    if (openButtons.length > 0) {
        openButtons.forEach((button) => {
            button.addEventListener('click', () => {
                var nearestFile = button.closest('.ufru-file-preview').querySelector('img');
                if (nearestFile) {
                    window.open(nearestFile.src, '_blank');
                }
            });
        });
    }

    $("[js-upload-files-form-submit]").click(function(event) {
        var $fileUpload = $("#file_upload");
        if (parseInt($fileUpload.get(0).files.length) > 10) {
            alert("You are only allowed to upload a maximum of 10 files");
            event.preventDefault();
        }
    });
});