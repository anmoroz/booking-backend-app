<?php
/*
 * This file is part of the Booking application project.
 *
 * https://github.com/anmoroz
 */

declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationException extends BadRequestHttpException
{
    protected array $validationErrors = [];

    /**
     * @param array $validationErrors
     */
    public function __construct(array $validationErrors)
    {
        parent::__construct();
        $this->validationErrors = $validationErrors;
    }

    public static function createFromConstraintViolations(ConstraintViolationListInterface $violationList): self
    {
        $validationErrors = [];

        /** @var ConstraintViolationInterface $violation */
        foreach ($violationList as $violation) {
            $validationErrors[$violation->getPropertyPath()][] = $violation->getMessage();
        }

        return new self($validationErrors);
    }

    /**
     * @return array
     */
    public function getValidationErrors(): array
    {
        return $this->validationErrors;
    }
}