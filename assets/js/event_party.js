'use strict';

import '../css/event_party.scss';

import $ from 'jquery';
import 'bootstrap';
import Chat from './components/eventParty/Chat';
import Receiver from './components/eventParty/Receiver';

$(document).ready(() => {
    new Chat();
    new Receiver();
});

