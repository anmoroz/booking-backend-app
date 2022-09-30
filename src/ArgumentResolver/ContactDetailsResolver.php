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
use App\Model\ContactDetails;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ContactDetailsResolver extends RequestArgumentResolverAbstract
{
    /**
     * @param ValidatorInterface $validator
     */
    public function __construct(private ValidatorInterface $validator)
    {
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return ContactDetails::class === $argument->getType();
    }

    /**
     * @param Request $request
     * @param ArgumentMetadata $argument
     * @return iterable
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $phone = $this->getStringParameter($request, 'phone', '');
        $name = $this->getStringParameter($request, 'name', '');
        $note = $this->getStringParameter($request, 'note', '');

        $contactDetails = new ContactDetails($phone, $name, $note);
        $contactDetails->setIsBanned(
            $this->getBooleanParameter($request, 'isBanned', false)
        );

        $errors = $this->validator->validate($contactDetails);

        if (count($errors) > 0) {
            throw ValidationException::createFromConstraintViolations($errors);
        }

        yield $contactDetails;
    }

}