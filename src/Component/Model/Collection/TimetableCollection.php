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

    /**
     * @param int $weekDay
     * @return Timetable[]
     */
    public function getForWeekDay(int $weekDay): array
    {
        return \array_filter($this->get(), function (Timetable $timetable) use ($weekDay) {
            return $timetable->getWeekDay() === $weekDay;
        });
    }
}