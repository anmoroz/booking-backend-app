<?php
/*
 * This file is part of the Reservation application project.
 *
 * https://github.com/anmoroz
 */

declare(strict_types=1);


namespace App\Core\ArgumentResolver;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;

abstract class RequestArgumentResolverAbstract implements ArgumentValueResolverInterface
{
    protected function getStringParameter(Request $request, string $key, string $defaultValue = null): ?string
    {
        $value = $request->get($key);

        if (!is_string($value)) {

            return $defaultValue;
        }

        return $value;
    }

    protected function getArrayParameter(Request $request, string $key, array $defaultValue = null): ?array
    {
        $value = $request->get($key);

        if (!is_array($value)) {

            return $defaultValue;
        }

        return $value;
    }

    protected function getNumberParameter(Request $request, string $key, ?float $defaultValue = null): ?float
    {
        $value = $request->get($key, $defaultValue);

        if (!is_numeric($value) && !is_null($value)) {

            return $defaultValue;
        }

        return (float) $value;
    }

    protected function getIntegerParameter(Request $request, string $key, int $defaultValue = null): ?int
    {
        $value = $request->get($key);

        if (!is_int($value)) {

            return $defaultValue;
        }

        return $value;
    }

    protected function getBooleanParameter(Request $request, string $key, bool $defaultValue = null): ?bool
    {
        $value = $request->get($key);
        if (!is_bool($value)) {

            return $defaultValue;
        }

        return $value;
    }

    /**
     * @param Request $request
     * @param string $key
     * @param int|null $defaultValue
     * @return int|null
     */
    protected function getIntegerQueryParameter(Request $request, string $key, int $defaultValue = null): ?int
    {
        $value = $request->query->get($key);

        if (!filter_var($value, FILTER_VALIDATE_INT)) {

            return $defaultValue;
        }

        return (int) $value;
    }

    protected function getStringQueryParameter(Request $request, string $key, string $defaultValue = null): ?string
    {
        $value = $request->query->get($key);

        if (empty($value)) {

            return $defaultValue;
        }

        return $value;
    }

    protected function getArrayQueryParameter(Request $request, string $key, array $defaultValue = null): ?array
    {
        $value = $request->query->all($key);

        if (!is_array($value)) {

            return $defaultValue;
        }

        return $value;
    }
}