Dropzone.autoDiscover = false;

jQuery(document).ready(function($) {
    // var myDropzone = new Dropzone('#my-dropzone', {
    //     url: '/',
    //     autoProcessQueue: false, // Disable automatic upload
    // });

    // $('form').on('submit', function(e) {
    //     e.preventDefault();
    //     myDropzone.processQueue(); // Manually trigger file uploads
    // });

    var openButtons = document.querySelectorAll('[js-ufru-open-image]');


    // document.getElementById('image').addEventListener('click', function() {
    //     window.open(this.src, '_blank');
    // });

    if (openButtons.length > 0) {

    console.log(openButtons);
        openButtons.forEach((button) => {
            button.addEventListener('click', (e) => {
                console.log(e.target, this);
                var nearestImage = button.closest('.ufru-image-preview').querySelector('img');
                if (nearestImage) {
                    window.open(nearestImage.src, '_blank');
                }
                // window.open(this.src, '_blank');
            });
        });
    }
});