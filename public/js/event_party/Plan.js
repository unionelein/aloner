class Plan {
    constructor() {
        this.helper = new Helper();

        this.$meetingPointOfferModal = $('#meetingPointOfferModal');
        this.$meetingPointOfferBody  = $('.meeting-point-offer-body');
        this.$meetingPointOfferFooter  = $('.meeting-point-offer-footer');

        $('.open-meeting-point-offer').on('click', this.handleMeetingPointOfferClick.bind(this));

        this.initAlert();
    }

    initAlert() {
        $('.accept-meeting-point-offer, .reject-meeting-point-offer').off()
            .on('click', this.handleAnswerMeetingPointOffer.bind(this));
    }

    addMeetingPointOfferAlert(offerId, meetingPointText) {
        this.helper.addMeetingPointOfferAlert(offerId, meetingPointText);

        this.initAlert();
    }

    handleMeetingPointOfferClick(e) {
        const url = $(e.currentTarget).data('url');

        this.$meetingPointOfferBody.html(this.helper.$loadSpinner);
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
            },

        });
    }

    handleAnswerMeetingPointOffer(e) {
        const $btn    = $(e.currentTarget);
        const $alert  = $btn.closest('.meeting-point-alert');

        const offerId = $alert.data('offer-id');
        const answer  = +$btn.data('answer');
        const url     = $alert.data('url');

        $.ajax({url: `${url}?answer=${answer}&offer_id=${offerId}`});

        $alert.hide(300);

        this.helper.updatePlanAlertsCount(-1);
    }
}