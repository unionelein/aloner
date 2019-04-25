class Plan {
    constructor() {
        this.$meetingPointOfferModal = $('#meetingPointOfferModal');
        this.$meetingPointOfferBody  = $('.meeting-point-offer-body');
        this.$meetingPointOfferFooter  = $('.meeting-point-offer-footer');

        this.$loadSpinner = $('.js-data').find('.load-spinner');

        $('.open-meeting-point-offer').on('click', this.handleMeetingPointOfferClick.bind(this));

    }

    handleMeetingPointOfferClick(e) {
        const url = $(e.currentTarget).data('url');

        this.$meetingPointOfferBody.html(this.$loadSpinner);
        this.$meetingPointOfferFooter.hide();

        $.ajax({
            url: url,
            success: (html) => {
                this.$meetingPointOfferBody.html(html);
                this.$meetingPointOfferFooter.show();

                $('.send-meeting-point-offer').off().on('click', this.handleSendMeetingPointClick.bind(this));
            },
            statusCode: {
                403: () => this.$meetingPointOfferBody.html('Доступ запрещен')
            }
        });
    }

    handleSendMeetingPointClick(e) {
        const $btn  = $(e.currentTarget);
        const $form = this.$meetingPointOfferBody.find('form');

        $btn.prop('disabled', true);

        $.ajax({
            url: $btn.data('url'),
            method: 'POST',
            data: $form.serialize(),
            success: (data) => {
                $btn.prop('disabled', false);

                if (data.status === 'success') {
                    this.$meetingPointOfferModal.modal('hide');
                }

                this.$meetingPointOfferBody.html(data);
            }
        });
    }
}