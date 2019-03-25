class Chat {

    static get TYPE_IDENTIFY() {
        return 'identify';
    }

    static get TYPE_MESSAGE() {
        return 'message';
    }

    constructor() {
        this.$currentUser = $('#current-user');
        this.$chatSendMsgBtn = $('.chat-send-message-btn');

        $.ajax(this.$currentUser.data('temp-hash-url')).done((data) => {
            this.userTempHash = data.tempHash;
            this.setUpConnection();
        });

        this.$chatSendMsgBtn.on('click', this.handleClickSendMessageBtn.bind(this));
    }

    setUpConnection() {
        this.connection = new WebSocket('ws://localhost:8080/chat');

        this.connection.onopen = () => {
            this.sendData({
                type: Chat.TYPE_IDENTIFY,
                userTempHash: this.userTempHash
            });
        };

        this.connection.onmessage = (e) => {
            let data = JSON.parse(e.data);
            this.appendMessage(data.username, data.message);
        };

        this.connection.onerror = (e) => {
            console.error(e);
        };
    }

    sendData(data) {
        this.connection.send(JSON.stringify(data));
    }

    handleClickSendMessageBtn(e) {
        const $input = $('#chat-message-input');

        this.sendMessage($input.val());

        $input.val('');
    }

    appendMessage (username, message) {
        const $msg  = $('<p>').html(`<b>${username}</b>: ${message}`);

        $('.chat-wrapper').append($msg);
    }

    sendMessage (text) {
        if (!text) {
            return;
        }

        this.clientData.message = text;

        this.connection.send(JSON.stringify(this.clientData));

        this.appendMessage(this.clientData.username, this.clientData.message);
    }
};