class Plan {
    constructor() {
        this.$timeOfferBody  = $('.time-offer-body');
        this.$placeOfferBody = $('.place-offer-body');

        this.$timeOfferFooter  = $('.time-offer-footer');
        this.$placeOfferFooter = $('.place-offer-footer');

        this.$loadSpinner = $('.js-data').find('.load-spinner');

        $('.js-time-offer').on('click', this.handleTimeOfferClick.bind(this));
        $('.js-place-offer').on('click', this.handlePlaceOfferClick.bind(this));
    }

    handleTimeOfferClick(e) {
        const url = $(e.currentTarget).data('url');

        this.$timeOfferBody.html(this.$loadSpinner);
        this.$timeOfferFooter.hide();

        $.ajax({
            url: url,
            success: (html) => {
                this.$timeOfferFooter.show();
                this.$timeOfferBody.html(html);
            },
            statusCode: {
                403: () => this.$timeOfferBody.html('Доступ запрещен')
            }
        });
    }

    handlePlaceOfferClick(e) {
        const url = $(e.currentTarget).data('url');

        this.$placeOfferFooter.hide();
        this.$placeOfferBody.html(this.$loadSpinner);

        $.ajax({
            url: url,
            success: (html) => {
                this.$placeOfferFooter.show();
                this.$placeOfferBody.html(html);
            },
            statusCode: {
                403: () => this.$placeOfferBody.html('Доступ запрещен')
            }
        });
    }
}