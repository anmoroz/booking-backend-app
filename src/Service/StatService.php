<?php
/*
 * This file is part of the Booking application project.
 *
 * https://github.com/anmoroz
 */

declare(strict_types=1);


namespace App\Service;

use App\Entity\Room;
use App\Model\ReservationStatItem;
use Doctrine\DBAL\Connection;

class StatService
{
    private const RUSSIAN_MONTHS = [
        1 => 'Янв',
        2 => 'Фев',
        3 => 'Март',
        4 => 'Апр',
        5 => 'Май',
        6 => 'Июнь',
        7 => 'Июль',
        8 => 'Авг',
        9 => 'Сент',
        10 => 'Окт',
        11 => 'Нояб',
        12 => 'Дек',
    ];

    public function __construct(private Connection $connection)
    {
    }

    /**
     * @param Room $room
     * @param int $year
     * @return array
     */
    public function reservationStatistics(Room $room, int $year): array
    {
        $resultSet = $this->connection->fetchAllAssociative(
            'SELECT
                    MONTH(checkin) as monthNumber,
                    SUM(DATEDIFF(checkout, checkin)) AS days,
                    SUM(price) as amount
                FROM reservation
                WHERE room_id = :roomId
                    AND contact_id IS NOT NULL
                    AND YEAR(checkin) = :year
                GROUP BY MONTH(checkin)',
            [
                'roomId' => $room->getId(),
                'year' => $year
            ]
        );

        $resultStat = [];
        for ($month = 1; $month <= 12; $month++) {
            array_push($resultStat, $this->buildReservationStatItem($resultSet, $month));
        }

        return $resultStat;
    }

    /**
     * @param array $resultSet
     * @param int $month
     * @return ReservationStatItem
     */
    private function buildReservationStatItem(array $resultSet, int $month): ReservationStatItem
    {
        foreach ($resultSet as $item) {
            if ((int) $item['monthNumber'] === $month) {

                return new ReservationStatItem(
                    self::RUSSIAN_MONTHS[$month],
                    (int) $item['days'],
                    (float) $item['amount']
                );
            }
        }

        return new ReservationStatItem(self::RUSSIAN_MONTHS[$month]);
    }
}