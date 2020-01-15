<?php declare(strict_types=1);

namespace App\DataFixtures;

use App\Component\Util\Date;
use App\Component\Util\Week;
use App\Entity\City;
use App\Entity\Event;
use App\Entity\Media;
use App\Entity\Timetable;
use App\Entity\VO\Contacts;
use App\Entity\VO\Range;

class EventFixture extends BaseFixture
{
    public const EVENTS_COUNT = 40;

    private const EVENTS = [
        'Батутная арена Neo Land',
        'Боулинг в Спартаке',
        'Мозгобойня',
        'Клуб Мята',
        'Квест Тайная комната',
        'Квест Побег',
        'Квест Паранойа',
        'Ночная тусовка на батутах (Neo Land)',
        'Парк аттракционов',
        'Прогулка по парку Румянцевых-Паскевичей',
        'Лазертаг в Корсаре'
    ];

    private const DURATIONS = [
        45,
        60,
        90,
        120,
    ];

    private const ADDRESSES = [
        'г.Гомель, ул.Коммунаров, 6',
        'г.Гомель, ул.Советская, 63',
        'г.Гомель, ул.Ильича, 331',
        'г.Гомель, пр-т. Ленина, 2-4',
        'г.Гомель, пр-т. Ленина, 33',
        'г.Гомель, а/я 227',
    ];

    private const PHONES = [
        null,
        '8 044 791-97-00',
        '8 0232 60-31-05',
        '+375 44 553-65-55',
    ];

    private const SITES = [
        null,
        'https://neoland.by/',
        'корсар.бел',
        'http://gomel.mir-kvestov.by/companies/paranojja-gomel',
        'http://www.gomelpark.by/',
    ];

    private const YANDEX_MAPS = [
        null,
        'https://api-maps.yandex.ru/services/constructor/1.0/js/?um=constructor%3A2e1c2828ada34d35b904da18a05c22fbb6d6e3cefe4bc77f09bb381d4ccc6b4d&amp;width=650&amp;height=350&amp;lang=ru_RU&amp;scroll=true',
    ];

    private const DESCRIPTIONS = [
        '',
        <<<EOF
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
        ,
        <<<EOF
Основные понятия

В настоящих Правилах применяют следующие термины с соответствующими определениями:

Центр – Государственное учреждение «Гомельский областной центр олимпийского резерва по водным видам спорта и фристайлу »

Услуга – деятельность Центра по удовлетворению потребностей потребителя в поддержании и укреплении здоровья, а также проведении физкультурно-оздоровительного и спортивного досуга.

Посетитель Центра – гражданин (организация), посещающие Центр с целью получения услуги.
EOF
        ,
    ];

    public function loadData(): void
    {
        $this->createMany('event', self::EVENTS_COUNT, function (int $index) {
            $name     = $this->faker->randomElement(self::EVENTS);
            $duration = $this->faker->randomElement(self::DURATIONS);

            /** @var City $city */
            $city     = $this->getReference('city_' . $this->faker->numberBetween(1, CityFixture::citiesCount()));
            $address  = $this->faker->randomElement(self::ADDRESSES);
            $contacts = new Contacts($address, $city);
            $contacts->setPhone($this->faker->randomElement(self::PHONES));
            $contacts->setSite($this->faker->randomElement(self::SITES));
            $contacts->setYandexMap($this->faker->randomElement(self::YANDEX_MAPS));

            $maxPeople   = $this->faker->numberBetween(4, 10);
            $minPeople   = $this->faker->numberBetween(2, \min($maxPeople, 6));
            $peopleRange = new Range($minPeople, $maxPeople);

            $event = new Event($name, $duration, $contacts, $peopleRange);
            $event->setReservationRequired($this->faker->randomElement([true, false]));
            $event->setPrice($this->faker->numberBetween(20, 200) / 10 . ' BYN');
            $event->setDescription($this->faker->randomElement(self::DESCRIPTIONS));

            $this->addMedia($event);
            $this->addTimeTables($event);

            return $event;
        });
    }

    /**
     * @param Event $event
     */
    private function addMedia(Event $event): void
    {
        static $media = [];

        if (!$media) {
            $media[] = new Media('build/static/img/event/neoland_1.jpg', Media::TYPE_IMAGE, 'батуты');
            $media[] = new Media('build/static/img/event/neoland_2.jpg', Media::TYPE_IMAGE, 'паралоновые кубики');
            $media[] = new Media('build/static/img/event/neoland_3.jpg', Media::TYPE_IMAGE);
            $media[] = new Media('build/static/video/event/gorka.mp4', Media::TYPE_VIDEO, 'Видео прыжков', 'build/static/img/event/neoland_3.jpg');
        }

        foreach ($media as $oneMedia) {
            $event->addMedia($oneMedia);
        }
    }

    /**
     * @param Event $event
     */
    private function addTimeTables(Event $event): void
    {
        foreach (Week::DAYS as $weekDay => $name) {
            $type = $this->faker->randomElement(Timetable::TYPES);

            $startHour = $this->faker->randomElement([9, 15]);
            $startMin  = $this->faker->randomElement([0, 15, 30, 45]);
            $startTime = Date::time(\sprintf('%d:%d:00', $startHour, $startMin));

            $endHour = $this->faker->randomElement([18, 23]);
            $endMin  = $this->faker->randomElement([0, 15, 30, 45]);
            $endTime = Date::time(\sprintf('%d:%d:00', $endHour, $endMin));

            if ($type === Timetable::TYPE_DAY) {
                $timetable = new Timetable($event, $type, $weekDay, $startTime, $endTime);
                $event->addTimetable($timetable);
                continue;
            }

            if ($type === Timetable::TYPE_VISIT) {
                $duration = $event->getDuration();
                $time     = clone $startTime;

                while ($time < $endTime) {
                    $start = clone $time;
                    $end   = clone $time->modify("+ {$duration}min");

                    $timetable = new Timetable($event, $type, $weekDay, $start, $end);
                    $event->addTimetable($timetable);
                }
                continue;
            }
        }
    }

    /**
     * @return array
     */
    public function getDependencies(): array
    {
        return [
            CityFixture::class,
        ];
    }
}
