$(document).ready(() => {
    new Chat();
    new Announcement();

    const gallery = document.getElementById('event-gallery');
    lightGallery(gallery, {
        videojs: true,
        thumbnail:true,
        animateThumb: false,
        showThumbByDefault: false
    });
});