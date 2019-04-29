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

    static get TYPE_MEETING_POINT_OFFER_ANSWER() {
        return 'meeting_point_offer_answer';
    }

    static get TYPE_MEETING_POINT_OFFER_ACCEPTED() {
        return 'meeting_point_offer_accepted';
    }

    constructor() {
        this.helper = new Helper();
        this.plan   = new Plan();

        this.$userData = $('#current-user-data');
        this.$epData   = $('#event-party-data');

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
                    case Receiver.TYPE_MEETING_POINT_OFFER_ANSWER: return this.onMeetingPointOfferAnswer(data.data);
                    case Receiver.TYPE_MEETING_POINT_OFFER_ACCEPTED: return this.onMeetingPointOfferAccepted(data.data);
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

        this.helper.addUserIconBlock(data.userId, data.avatarPath, data.nickname);
        this.helper.updateEpStatus(data.epStatus);
    }

    onSkip(data) {
        if (this.$userData.data('id') === data.userId) {
            return
        }

        this.helper.removeUserIconBlock(data.userId);
        this.helper.updateEpStatus(data.epStatus);

        this.helper.hideTab('plan');
        this.helper.openTab('info');

        this.helper.resetPlanTab();
    }

    onFilled(data) {
        this.helper.openTab('plan');
    }

    onMeetingPointOffer(data) {
        const meetingPointText = `${data.place} - ${data.meetingDateTimeString}`;

        this.helper.addMeetingPointOfferHistoryRow(data.offerId, meetingPointText);

        if (this.$userData.data('id') !== data.userId) {
            this.plan.addMeetingPointOfferAlert(data.offerId, meetingPointText);
        }
    }

    onMeetingPointOfferAnswer(data) {
        data.answer
            ? this.helper.addAcceptedAnswerToMeetingPointHistoryRow(data.offerId)
            : this.helper.markMeetingPointHistoryRowAsRejected(data.offerId);
    }

    onMeetingPointOfferAccepted(data) {
        this.helper.updateEpStatus(data.epStatus);
        this.helper.updateMeetingPlace(data.place);
        this.helper.updateMeetingDateTime(data.meetingDateTimeString);
    }
}
