class Pusher {
    constructor() {
        this.setUpConnection();
    }

    setUpConnection() {
        const onConnect = () => {
            const eventPartyHash = $('#event-party-data').data('hash');

            this.connection.subscribe(eventPartyHash, (topic, data) => {
                // This is where you would add the new article to the DOM (beyond the scope of this tutorial)
                console.log('New article published to category "' + topic + '" : ' + data.title);
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
