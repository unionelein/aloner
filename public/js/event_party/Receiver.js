class Receiver {
    constructor() {
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

            this.connection.subscribe(`${epId}`, (topic, data) => {
                console.log('topic id: ' + topic);
                console.log('data: ' + data);
            });
        };

        const onError = () => {
            console.warn('WebSocket connection closed');
        };

        this.connection = new ab.Session('ws://localhost:8888/pusher', onConnect, onError, {
            'skipSubprotocolCheck': true
        });
    }
}
