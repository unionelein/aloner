class Receiver {
    static get TYPE_JOIN() {
        return 'join';
    }

    static get TYPE_SKIP() {
        return 'skip';
    }

    static get TYPE_FILLED() {
        return 'filled';
    }

    static get TYPE_MEETING_POINT_OFFER() {
        return 'meeting_point_offer';
    }

    constructor() {
        this.$userData = $('#current-user-data');
        this.$epData   = $('#event-party-data');

        this.$epStatus      = $('.event-party-status');
        this.$planBody      = $('.tab-plan-body');
        this.$planTabNav    = $('#nav-plan-tab');
        this.$offersWrapper = $('.offers-wrapper');

        this.setUpConnection();
    }

    setUpConnection() {
        const onConnect = () => {
            const epId = this.$epData.data('id');
            const hash = this.$userData.data('temp-hash');

            this.connection.publish(`${epId}`, {
                eventPartyId: epId,
                userTempHash: hash
            });

            this.connection.subscribe(`${epId}`, (topic, jsonData) => {
                let data = JSON.parse(jsonData);

                if (!data.type || !data.data) {
                    return;
                }

                switch (data.type) {
                    case Receiver.TYPE_JOIN: return this.onJoin(data.data);
                    case Receiver.TYPE_SKIP: return this.onSkip(data.data);
                    case Receiver.TYPE_FILLED: return this.onFilled(data.data);
                    case Receiver.TYPE_MEETING_POINT_OFFER: return this.onMeetingPointOffer(data.data);
                }
            });
        };

        const onError = () => {
            console.warn('WebSocket connection closed');
        };

        this.connection = new ab.Session('ws://localhost:8888/pusher', onConnect, onError, {
            'skipSubprotocolCheck': true
        });
    }

    onJoin(data) {
        if (this.$userData.data('id') === data.userId) {
            return
        }

        const $participantsWrapper = $('.participants-wrapper');
        const $userIconBlock       = $participantsWrapper.find('.user-icon-block').first().clone();

        $userIconBlock.removeClass('current-user').addClass(`js-user-icon-block-${data.userId}`);
        $userIconBlock.find('.user-icon-img').attr('src', `/${data.avatarPath}`);
        $userIconBlock.find('.user-icon-name').html(data.nickName);

        $participantsWrapper.append($userIconBlock);

        this.$epStatus.html(data.eventPartyStatus);
    }

    onSkip(data) {
        if (this.$userData.data('id') === data.userId) {
            return
        }

        $('#nav-plan-tab').addClass('d-none');
        $('#nav-plan').addClass('d-none');
        this.openTab('info');

        $(`.js-user-icon-block-${data.userId}`).remove();
        this.$epStatus.html(data.eventPartyStatus);
    }

    onFilled(data) {
        $('#nav-plan-tab').removeClass('d-none');
        $('#nav-plan').removeClass('d-none');
        this.openTab('plan');
    }

    openTab(name) {
        $('.event-party-tabs-wrapper').find('.nav-item').removeClass('active show')
            .attr('aria-selected', 'false');

        $('.nav-block-content-wrapper').find('.tab-pane').removeClass('active show');

        $(`#nav-${name}-tab`).addClass('active show')
            .attr('aria-selected', 'true');

        $(`#nav-${name}`).addClass('active show');
    }

    onMeetingPointOffer(data) {
        let $meetingPointAlert = this.$offersWrapper.find('.meeting-point-alert').clone();

        $meetingPointAlert.find('.meeting-point-text').html(`${data.place} - ${data.meetingDateTimeString}`);
        this.$planBody.prepend($meetingPointAlert);

        let $badge = this.$planTabNav.find('.badge');

        if (!$badge.length) {
            $badge = $('<span>').addClass('badge badge-info').html('0');
            this.$planTabNav.append($badge);
        }

        let count = +$badge.html();
        $badge.html(count + 1);
    }
}
