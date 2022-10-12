<?php
/*
 * This file is part of the Booking application project.
 *
 * https://github.com/anmoroz
 */

declare(strict_types=1);


namespace App\Model;


use Symfony\Component\Serializer\Annotation\Groups;

class ReservationStatItem
{
    #[Groups(["show"])]
    private string $month;

    #[Groups(["show"])]
    private int $days;

    #[Groups(["show"])]
    private float $amount;

    /**
     * @param string $month
     * @param int $days
     * @param float $amount
     */
    public function __construct(string $month, int $days = 0, float $amount = 0.0)
    {
        $this->month = $month;
        $this->days = $days;
        $this->amount = $amount;
    }

    /**
     * @return string
     */
    public function getMonth(): string
    {
        return $this->month;
    }

    /**
     * @return int
     */
    public function getDays(): int
    {
        return $this->days;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }
}