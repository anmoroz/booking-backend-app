<?php

declare(strict_types=1);


namespace App\ArgumentResolver;


use App\Exception\ValidationException;
use App\Security\UserCredentials;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserCredentialsResolver implements ArgumentValueResolverInterface
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
        $email = $request->get('email', '');
        $password = $request->get('password', '');

        $credentials = new UserCredentials($email, $password);

        $errors = $this->validator->validate($credentials);

        if (count($errors) > 0) {
            throw ValidationException::createFromConstraintViolations($errors);
        }

        yield $credentials;
    }
}