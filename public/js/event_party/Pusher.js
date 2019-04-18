class Pusher {
    constructor() {
        this.setUpConnection();
    }

    setUpConnection() {
        const onConnect = () => {
            const hash = $('#current-user-data').data('temp-hash');
            const epId = $('#event-party-data').data('id');

            this.connection.subscribe(`${epId}|${hash}`, (topic, data) => {
                // This is where you would add the new article to the DOM (beyond the scope of this tutorial)
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
