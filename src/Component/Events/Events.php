<?php declare(strict_types=1);

namespace App\Component\Events;

class Events
{
    public const EP_LOAD = 'event_party.load';

    public const EP_JOIN = 'event_party.join';

    public const EP_SKIP = 'event_party.skip';

    public const EP_FILLED = 'event_party.filled';

    public const MO_OFFERED = 'meeting_options.offered';

    public const MO_ANSWERED = 'meeting_options.answered';

    public const MO_ACCEPTED = 'meeting_options.accepted';
}
