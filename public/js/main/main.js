$(document).ready(() => {
    $('.search-event-party-btn').on('click', (e) => {
        $(e.currentTarget).prop('disabled', true);
    })
});