<?php
/*
 * This file is part of the Booking application project.
 *
 * https://github.com/anmoroz
 */

declare(strict_types=1);


namespace App\ArgumentResolver;

use App\Core\ArgumentResolver\RequestArgumentResolverAbstract;
use App\Model\RefreshTokenDTO;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class RefreshTokenDTOResolver extends RequestArgumentResolverAbstract
{
    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return RefreshTokenDTO::class === $argument->getType();
    }

    /**
     * @param Request $request
     * @param ArgumentMetadata $argument
     * @return iterable
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $refreshToken = $this->getStringParameter($request, 'refreshToken');
        if (is_null($refreshToken)) {
            throw new BadRequestException('Не указан обязательный параметр "refreshToken"');
        }

        yield new RefreshTokenDTO($refreshToken);
    }
}