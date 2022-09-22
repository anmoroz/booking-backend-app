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
use App\Security\UserCredentials;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserCredentialsResolver extends RequestArgumentResolverAbstract
{
    /**
     * @param ValidatorInterface $validator
     */
    public function __construct(private ValidatorInterface $validator)
    {
    }

    /**
     * @param Request $request
     * @param ArgumentMetadata $argument
     * @return bool
     */
    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return UserCredentials::class === $argument->getType();
    }

    /**
     * @param Request $request
     * @param ArgumentMetadata $argument
     * @return iterable
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $email = $this->getStringParameter($request, 'email', '');
        $password = $this->getStringParameter($request, 'password', '');

        $credentials = new UserCredentials($email, $password);

        $errors = $this->validator->validate($credentials);

        if (count($errors) > 0) {
            throw ValidationException::createFromConstraintViolations($errors);
        }

        yield $credentials;
    }
}