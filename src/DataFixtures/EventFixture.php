<?php declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\City;
use App\Entity\Event;
use App\Entity\Media;
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
            $city  = $this->getReference('city_1');
            $title = $this->faker->randomElement(self::EVENT_TITLES);

            $event = new Event($title, $city);
            $event->setDescription(
                'Батутная арена это отличное место повеселиться с детьми, друзьями и самостоятельно получить заряд позитива.
                
                12 батутов(один из которых гимнастический), самая большая поролоновая яма в Беларуси в которую можно прыгать как с батутов и тумб (парапетов), так и со стены для скалолазания, канат, два настольных футбола(кикер), аэрохоккей, баскетбольное кольцо, Sony Playstation 4, настольный тенниc, а так-же крутая фото зона.
                Все эти радости входят в стоимость входного билета.'
            );
            $event->setPhone('+375292052239');
            $event->setAddress('Гомель, ул. Советская, ' . \rand(1, 100));
            $event->setSite('https://carte.by/gomel/bubbles/');

            $event->addMedia(new Media('/media/img/neoland_1.jpg', Media::TYPE_IMAGE, 'батуты'));
            $event->addMedia(new Media('/media/img/neoland_2.jpg', Media::TYPE_IMAGE, 'паралоновые кубики'));
            $event->addMedia(new Media('/media/img/neoland_3.jpg', Media::TYPE_IMAGE));
            $event->addMedia(new Media('/media/video/gorka.mp4', Media::TYPE_VIDEO));
            $event->addMedia(
                new Media(
                    '//vk.com/video_ext.php?oid=-162658447&id=456239021&hash=37e3bddfeff1ad45&hd=2',
                    Media::TYPE_IFRAME,
                    'микрочелики прыгают на батутах'
                )
            );

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
                $interval  = $this->faker->randomElement([45, 60, 120]);
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
