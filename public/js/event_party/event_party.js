$(document).ready(() => {
    new Chat();
    new Receiver();
    new Plan();

    $('#event-gallery').lightGallery({
        videojs: true
    });
});

