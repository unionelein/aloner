'use strict';

import $ from 'jquery';
import 'bootstrap';

import '../css/event_party.scss';

import Chat from './Components/eventParty/Chat';
import Receiver from './Components/eventParty/Receiver';

$(document).ready(() => {
    new Chat();
    new Receiver();
});

