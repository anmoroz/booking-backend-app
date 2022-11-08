<?php
/*
 * This file is part of the Booking application project.
 *
 * https://github.com/anmoroz
 */

declare(strict_types=1);


namespace App\ArgumentResolver\User;


use App\Core\ArgumentResolver\RequestArgumentResolverAbstract;
use App\Core\Exception\ValidationException;
use App\Model\EmailDTO;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EmailDTOResolver extends RequestArgumentResolverAbstract
{
    /**
     * @param ValidatorInterface $validator
     */
    public function __construct(private ValidatorInterface $validator)
    {
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return EmailDTO::class === $argument->getType();
    }

    /**
     * @param Request $request
     * @param ArgumentMetadata $argument
     * @return iterable
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $email = $this->getStringParameter($request, 'email', '');

        $emailDTO = new EmailDTO($email);
        $errors = $this->validator->validate($emailDTO);

        if (count($errors) > 0) {
            throw ValidationException::createFromConstraintViolations($errors);
        }

        yield $emailDTO;
    }

}