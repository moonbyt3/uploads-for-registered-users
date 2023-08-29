Dropzone.autoDiscover = false;

jQuery(document).ready(function ($) {
    var openButtons = document.querySelectorAll('[js-ufru-open-image]');

    if (openButtons.length > 0) {
        openButtons.forEach((button) => {
            button.addEventListener('click', (e) => {
                console.log(e.target, this);
                var nearestImage = button.closest('.ufru-image-preview').querySelector('img');
                if (nearestImage) {
                    window.open(nearestImage.src, '_blank');
                }
            });
        });
    }
});