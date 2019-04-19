class Receiver {
    static get TYPE_JOIN() {
        return 'join';
    }

    static get TYPE_SKIP() {
        return 'skip';
    }

    constructor() {
        this.$epStatus = $('.event-party-status');

        this.setUpConnection();
    }

    setUpConnection() {
        const onConnect = () => {
            const epId = $('#event-party-data').data('id');
            const hash = $('#current-user-data').data('temp-hash');

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
        const userIconBlockPrototype = $('.js-user-icon-block-prototype').html();

        let userIconBlock = userIconBlockPrototype
            .replace(/__userId__/g, data.userId)
            .replace(/__additionalClasses__/g, `js-user-icon-block-${data.userId}`)
            .replace(/__avatarPath__/g, `/${data.avatarPath}`)
            .replace(/__nickName__/g, data.nickName);

        $('.participants-wrapper').append(userIconBlock);
        this.$epStatus.html(data.eventPartyStatus);
    }

    onSkip(data) {
        $(`.js-user-icon-block-${data.userId}`).remove();
        this.$epStatus.html(data.eventPartyStatus);
    }
}
