<?php declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\City;
use App\Entity\Event;
use App\Entity\Timetable;

class EventFixture extends BaseFixture
{
    public const EVENT_TITLES = [
        'Батутная арена Neo Land',
        'Боулинг на спартаке',
        'Прыжки с парашютом',
        'Клуб Мята',
        'Квест Тайная комната',
        'Квест Побег',
        'Квест Паранойа',
        'Бильярд',
        'Ночная тусовка на батутах',
        'Музей картин 1950-ых',
        'Зоопарк',
        'Парк аттракционов',
        'Прогулка по парку румянцевых-паскевичей',
        'Картинг',
        'Казино "Твоя удача"',
    ];

    public function loadData()
    {
        $this->createMany('event', 20, function (int $index) {
            /** @var City $city */
            $city  = $this->getReference('city_' . \rand(1, \count(CityFixture::CITIES)));
            $title = $this->faker->randomElement(self::EVENT_TITLES);

            $event = new Event($title, $city);
            $event->setDescription($this->faker->text);
            $event->setPhone('+375292052239');
            $event->setAddress('Гомель, ул. Советская, ' . \rand(1, 100));
            $event->setSite('https://carte.by/gomel/bubbles/');

            $this->addTimeTables($event);

            return $event;
        });
    }

    private function addTimeTables(Event $event)
    {
        $type = $this->faker->randomElement(Timetable::TYPES);

        foreach (Timetable::WEEK_DAYS as $day => $name) {
            if ($type === Timetable::TYPE_DAY) {
                $timetable = new Timetable(
                    $event,
                    $day,
                    new \DateTime(\sprintf('%d:%d0:00', \rand(8, 11), $this->faker->randomElement([0, 3]))),
                    new \DateTime(\sprintf('%d:%d0:00', \rand(15, 23), $this->faker->randomElement([0, 3]))),
                    $type
                );
                $event->addTimetable($timetable);
            }

            if ($type === Timetable::TYPE_VISIT) {
                $interval  = $this->faker->randomElement([15, 30, 45, 60]);
                $total     = 600 / $interval + \rand(-3, +3);
                $workStart = \rand(7, 12);
                for ($i = 0; $i < $total; $i++) {
                    $timetable = new Timetable(
                        $event,
                        $day,
                        (new \DateTime("{$workStart}:00:00"))->modify('+' . $i * $interval . ' min'),
                        (new \DateTime("{$workStart}:00:00"))->modify('+' . ($i + 1) * $interval . ' min'),
                        $type
                    );
                    $event->addTimetable($timetable);
                }
            }
        }
    }

    public function getDependencies()
    {
        return [
            CityFixture::class,
        ];
    }
}