<?php
/*
 * This file is part of the Reservation application project.
 *
 * https://github.com/anmoroz
 */

declare(strict_types=1);


namespace App\ArgumentResolver;

use App\Core\ArgumentResolver\RequestArgumentResolverAbstract;
use App\Core\Exception\ValidationException;
use App\Model\ContactDetails;
use App\Model\ReservationDetails;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use DateTime;

class ReservationDetailsResolver extends RequestArgumentResolverAbstract
{
    /**
     * @param ValidatorInterface $validator
     */
    public function __construct(private ValidatorInterface $validator)
    {
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return ReservationDetails::class === $argument->getType();
    }

    /**
     * @param Request $request
     * @param ArgumentMetadata $argument
     * @return iterable
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $checkin = new DateTime($this->getStringParameter($request, 'checkin'));
        $checkout = new DateTime($this->getStringParameter($request, 'checkout'));
        $adults = $this->getIntegerParameter($request, 'adults', 0);
        $children = $this->getIntegerParameter($request, 'children', 0);

        $reservationDetails = new  ReservationDetails($checkin, $checkout, $adults, $children);

        $reservationDetails->setNote($this->getStringParameter($request, 'note', ''));



        $contactData = $this->getArrayParameter($request, 'contact');
        if (!is_null($contactData)) {
            $contactDetails = new ContactDetails(
                $contactData['phone'] ?? null,
                $contactData['name'] ?? null,
                $contactData['note'] ?? ''
            );

            $this->checkErrors($contactDetails);

            $reservationDetails->setContactDetails($contactDetails);
            $validationGroups = null;
        } else {
            $validationGroups = ['close_reservation'];
        }

        $this->checkErrors($reservationDetails, $validationGroups);

        yield $reservationDetails;
    }

    /**
     * @param $model
     * @param array|null $validationGroups
     * @return void
     */
    private function checkErrors($model, ?array $validationGroups = null): void
    {
        $errors = $this->validator->validate($model, null, $validationGroups);

        if (count($errors) > 0) {
            throw ValidationException::createFromConstraintViolations($errors);
        }
    }
}