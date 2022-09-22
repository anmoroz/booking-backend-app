<?php
/*
 * This file is part of the Reservation application project.
 *
 * https://github.com/anmoroz
 */

declare(strict_types=1);

namespace App\Core\Exception;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationException extends BadRequestHttpException
{
    public static function createFromConstraintViolations(ConstraintViolationListInterface $violationList): self
    {
        $messages = [];
        /** @var ConstraintViolationInterface $violation */
        foreach ($violationList as $violation) {
            array_push($messages, $violation->getMessage());
        }

        return new self(implode(PHP_EOL, $messages));
    }
}