<?php
/*
 * This file is part of the Reservation application project.
 *
 * https://github.com/anmoroz
 */

declare(strict_types=1);


namespace App\Core\Entity;

interface EntityInterface
{
    /**
     * @return int|null
     */
    public function getId(): ?int;
}