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

    constructor() {
        this.$userData = $('#current-user-data');
        this.$epData   = $('#event-party-data');

        this.$epStatus    = $('.event-party-status');
        this.$planTab     = $('#nav-plan-tab');
        this.$planBlock   = $('#nav-plan');
        this.$tabsWrapper = $('.event-party-tabs-wrapper');

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

        const userIconBlockPrototype = $('.js-user-icon-block-prototype').html();

        let userIconBlock = userIconBlockPrototype
            .replace(/__userId__/g, data.userId)
            .replace(/__additionalClasses__/g, `js-user-icon-block-${data.userId}`)
            .replace(/__avatar_path_src_attr__/g, `src="/${data.avatarPath}"`)
            .replace(/__nickName__/g, data.nickName);

        $('.participants-wrapper').append(userIconBlock);
        this.$epStatus.html(data.eventPartyStatus);
    }

    onSkip(data) {
        if (this.$userData.data('id') === data.userId) {
            return
        }

        $(`.js-user-icon-block-${data.userId}`).remove();
        this.$epStatus.html(data.eventPartyStatus);
    }

    onFilled(data) {
        this.showPlanTab();
    }

    showPlanTab() {
        this.$tabsWrapper.find('.nav-item').removeClass('active show')
            .attr('aria-selected', 'true');

        this.$tabsWrapper.find('.tab-pane').removeClass('active show');

        this.$planTab.addClass('active show')
            .attr('aria-selected', 'true')
            .show();

        this.$planBlock.addClass('active show')
            .show();
    }
}
