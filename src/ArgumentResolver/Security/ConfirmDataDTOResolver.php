<?php
/*
 * This file is part of the Booking application project.
 *
 * https://github.com/anmoroz
 */

declare(strict_types=1);


namespace App\ArgumentResolver\Security;

use App\Core\ArgumentResolver\RequestArgumentResolverAbstract;
use App\Core\Exception\FormValidationException;
use App\Model\Security\ConfirmDataDTO;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ConfirmDataDTOResolver extends RequestArgumentResolverAbstract
{
    /**
     * @param ValidatorInterface $validator
     */
    public function __construct(private ValidatorInterface $validator)
    {
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return ConfirmDataDTO::class === $argument->getType();
    }

    /**
     * @param Request $request
     * @param ArgumentMetadata $argument
     * @return iterable
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $token = $this->getStringParameter($request, 'token');
        $password = $this->getStringParameter($request, 'password');
        $name = $this->getStringParameter($request, 'name');

        $confirmDataDTO = new ConfirmDataDTO($token, $password, $name);

        $errors = $this->validator->validate($confirmDataDTO);

        if (count($errors) > 0) {
            throw FormValidationException::createFromConstraintViolations($errors);
        }

        yield $confirmDataDTO;
    }
}