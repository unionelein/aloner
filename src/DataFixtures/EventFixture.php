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

            $event = new Event($title, $city, \rand(2, 4), \rand(4, 6));
            $event->setDescription(<<<EOF
%tab% **Батутная арена** - это отличное место повеселиться и познакомиться, а так же получить заряд позитива!

%tab% 12 батутов, самая большая поролоновая яма, 
в которую можно прыгать, канат, два настольных футбола(кикер), 
аэрохоккей, баскетбольное кольцо, Sony Playstation 4, настольный тенниc, 
а так-же крутая фото зона.  
*Все это входит в стоимость одного билета*

%tab% Правила:

  + Заниматься на Батутной арене можно в спортивной одежде без жестких и острых элементов.
  + Если Вам нет 18 лет, то Вам необходимо принести письменное согласие родителей.
  + Ограничения по весу 90 кг.
EOF
            );
            $event->setPhone('+375292052239');
            $event->setAddress('Гомель, ул. Советская, ' . \rand(1, 100));
            $event->setSite('https://carte.by/gomel/bubbles/');
            $event->setPriceText('1 билет стоит ' . \rand(5, 15) . 'p.');
            $event->setYandexMapSrc('https://api-maps.yandex.ru/services/constructor/1.0/js/?um=constructor%3A2e1c2828ada34d35b904da18a05c22fbb6d6e3cefe4bc77f09bb381d4ccc6b4d&amp;width=650&amp;height=350&amp;lang=ru_RU&amp;scroll=true');

            $event->addMedia(new Media('/media/img/neoland_1.jpg', Media::TYPE_IMAGE, 'батуты'));
            $event->addMedia(new Media('/media/img/neoland_2.jpg', Media::TYPE_IMAGE, 'паралоновые кубики'));
            $event->addMedia(new Media('/media/img/neoland_3.jpg', Media::TYPE_IMAGE));
            $event->addMedia(new Media('/media/video/gorka.mp4', Media::TYPE_VIDEO, 'Видео прыжков', '/media/img/neoland_3.jpg'));

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
                    $type,
                    $this->faker->randomElement([60, 90, 120])
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
                        $type,
                        $interval
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
