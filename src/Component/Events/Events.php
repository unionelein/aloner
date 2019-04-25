<?php declare(strict_types=1);

namespace App\Component\Events;

class Events
{
    public const LOAD_EVENT_PARTY = 'event_party.load';

    public const JOIN_TO_EVENT_PARTY = 'event_party.join';

    public const SKIP_EVENT_PARTY = 'event_party.skip';

    public const EVENT_PARTY_FILLED = 'event_party.filled';

    public const MEETING_POINT_OFFERED = 'event_party.meeting_point_offered';
}
