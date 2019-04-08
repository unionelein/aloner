<?php declare(strict_types=1);

namespace App\Component\Model\Collection;

use App\Entity\Timetable;

class TimetableCollection
{
    private $timetables;

    public function __construct()
    {
        $this->timetables = new \SplObjectStorage();
    }

    public function add(Timetable $timetable): void
    {
        $this->timetables->attach($timetable);
    }

    /**
     * @return Timetable[]
     */
    public function get(): array
    {
        return \iterator_to_array($this->timetables);
    }
}