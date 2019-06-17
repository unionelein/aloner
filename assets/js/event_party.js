'use strict';

import '../css/event_party.scss';

import $ from 'jquery';
import 'bootstrap';
import Chat from './Components/eventParty/Chat';
import Receiver from './Components/eventParty/Receiver';

$(document).ready(() => {
    new Chat();
    new Receiver();
});

