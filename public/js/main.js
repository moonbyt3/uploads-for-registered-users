jQuery(document).ready(function ($) {
    // Open images in new tab
    var openButtons = document.querySelectorAll('[js-ufru-open-image]');

    if (openButtons.length > 0) {
        openButtons.forEach((button) => {
            button.addEventListener('click', () => {
                var nearestImage = button.closest('.ufru-image-preview').querySelector('img');
                if (nearestImage) {
                    window.open(nearestImage.src, '_blank');
                }
            });
        });
    }

    $("[js-upload-images-form-submit]").click(function(event) {
        var $fileUpload = $("#file_upload");
        if (parseInt($fileUpload.get(0).files.length) > 10) {
            alert("You are only allowed to upload a maximum of 10 files");
            event.preventDefault();
        }
    });
});