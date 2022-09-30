<?php
/*
 * This file is part of the Booking application project.
 *
 * https://github.com/anmoroz
 */

declare(strict_types=1);


namespace App\ArgumentResolver;

use App\Core\ArgumentResolver\RequestArgumentResolverAbstract;
use App\Core\Exception\ValidationException;
use App\Model\RoomDetails;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RoomDetailsResolver extends RequestArgumentResolverAbstract
{
    /**
     * @param ValidatorInterface $validator
     */
    public function __construct(private ValidatorInterface $validator)
    {
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return RoomDetails::class === $argument->getType();
    }

    /**
     * @param Request $request
     * @param ArgumentMetadata $argument
     * @return iterable
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $name = $this->getStringParameter($request, 'name', '');
        $address = $this->getStringParameter($request, 'address', '');
        $roomDetails = new RoomDetails($name, $address);

        $errors = $this->validator->validate($roomDetails);

        if (count($errors) > 0) {
            throw ValidationException::createFromConstraintViolations($errors);
        }

        yield $roomDetails;
    }
}