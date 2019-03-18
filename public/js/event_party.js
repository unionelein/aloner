$(document).ready(() => {
    let clientInformation = {
        username: $('#chat-send-message').data('user-name')
        // You can add more information in a static object
    };

    let conn = new WebSocket('ws://localhost:8080');

    conn.onopen = () => {
        console.info("Connection established succesfully");
    };

    conn.onmessage = (e) => {
        let data = JSON.parse(e.data);
        Chat.appendMessage(data.username, data.message);

        console.log(data);
    };

    conn.onerror = (e) => {
        alert("Error: something went wrong with the socket.");
        console.error(e);
    };

    $('#chat-send-message').on('click', (e) => {
        let msg = $('#chat-message-input').val();

        if (!msg) return;

        Chat.sendMessage(msg);
        $('#chat-message-input').val('');
    });

// Mini API to send a message with the socket and append a message in a UL element.
    var Chat = {
        appendMessage: function(username, message){
            const $msg  = $('<p>').html(`<b>${username}</b>: ${message}`);

            $('.chat-wrapper').append($msg);
        },
        sendMessage: function(text){
            clientInformation.message = text;
            // Send info as JSON
            conn.send(JSON.stringify(clientInformation));
            // Add my own message to the list
            this.appendMessage(clientInformation.username, clientInformation.message);
        }
    };
});