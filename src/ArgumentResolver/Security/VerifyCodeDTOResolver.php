<?php
/*
 * This file is part of the Booking application project.
 *
 * https://github.com/anmoroz
 */

declare(strict_types=1);


namespace App\ArgumentResolver\Security;

use App\Core\ArgumentResolver\RequestArgumentResolverAbstract;
use App\Model\Security\VerifyCodeDTO;
use App\Repository\UserCodeRepository;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class VerifyCodeDTOResolver extends RequestArgumentResolverAbstract
{
    /**
     * @param ValidatorInterface $validator
     */
    public function __construct(private UserCodeRepository $userCodeRepository)
    {
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return VerifyCodeDTO::class === $argument->getType();
    }

    /**
     * @param Request $request
     * @param ArgumentMetadata $argument
     * @return iterable
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $userCode = null;
        if ($codeStr = $this->getStringParameter($request, 'code')) {
            $userCode = $this->userCodeRepository->findOneByCode($codeStr);
            if (!$userCode) {
                throw new BadRequestException('Неверный код');
            }
        }

        $email = $this->getStringParameter($request, 'email', '');
        if (!$userCode || $userCode->getUser()->getEmail() !== $email) {
            throw new BadRequestException('Неверный код');
        }

        yield new VerifyCodeDTO($userCode, $email);
    }
}