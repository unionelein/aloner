class Chat {

    static get TYPE_IDENTIFY() {
        return 'identify';
    }

    static get TYPE_MESSAGE() {
        return 'message';
    }

    constructor() {
        this.$currentUser = $('#current-user');
        this.$sendMessageBtn = $('.chat-send-message-btn');
        this.$messageInput = $('.chat-message-input');
        this.$messagesBlock = $('.chat-messages-block');
        this.userTempHash = this.$currentUser.data('temp-hash');

        this.setUpConnection();

        this.$sendMessageBtn.on('click', this.handleClickSendMessageBtn.bind(this));
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
        this.sendMessage(this.$messageInput.val());

        this.$messageInput.val('');
    }

    appendMessage (username, message) {
        const $msg = $('<p>').html(`<b>${username}</b>: ${message}`);

        this.$messagesBlock.append($msg);
    }

    sendMessage (text) {
        if (!text) {
            return;
        }

        this.sendData({
            type: Chat.TYPE_MESSAGE,
            message: text
        });
    }
}