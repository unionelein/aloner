$(document).ready(() => {
    new Chat();
    new Receiver();

    $('#event-gallery').lightGallery({
        videojs: true
    });
});

