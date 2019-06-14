'use strict';

import $ from 'jquery';

class Chat {

    static get TYPE_IDENTIFY() {
        return 'identify';
    }

    static get TYPE_MESSAGE() {
        return 'message';
    }

    constructor() {
        this.$currentUserData = $('#current-user-data');
        this.$eventPartyData  = $('#event-party-data');

        this.$sendMessageBtn = $('.chat-send-message-btn');
        this.$messageInput = $('.chat-message-input');
        this.$messagesBlock = $('.chat-messages-block');

        this.scrollMessagesBlockToBottom();
        this.$messagesBlock.removeClass('invisible');

        this.setUpConnection();

        this.$sendMessageBtn.on('click', this.handleClickSendMessageBtn.bind(this));
        this.$messageInput.on('keypress', this.handleKeyPressAtInput.bind(this));
    }

    setUpConnection() {
        this.connection = new WebSocket('ws://localhost:8080/chat');

        this.connection.onopen = () => {
            this.sendData({
                type: Chat.TYPE_IDENTIFY,
                eventPartyId: this.$eventPartyData.data('id'),
                userTempHash: this.$currentUserData.data('temp-hash')
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
        e.preventDefault();

        this.sendMessage(this.$messageInput.val());

        this.$messageInput.val('');
    }

    handleKeyPressAtInput(e) {
        if (e.which === 13 && !e.shiftKey) {
            e.preventDefault();
            this.sendMessage(this.$messageInput.val());
            this.$messageInput.val('');
        }
    }

    scrollMessagesBlockToBottom() {
        this.$messagesBlock.animate({
            scrollTop: this.$messagesBlock.height() * 10
        }, 0);
    }

    appendMessage (username, message) {
        const $msg = $('<p>').html(`<b>${username}</b>: ${message}`);

        this.$messagesBlock.append($msg);
        this.scrollMessagesBlockToBottom();
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

export default Chat;
