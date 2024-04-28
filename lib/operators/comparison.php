<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Lib\Operators\Comparison;

use Closure;

/**
 * @param int $expectedValue
 * @return array{expectedValue: int, callback: Closure(int): bool}
 */
function equalTo(int $expectedValue): array
{
    return ['expectedValue' => $expectedValue, 'callback' => static fn (int $value): bool => $value ===  $expectedValue];
}

/**
 * @param int $expectedValue
 * @return array{expectedValue: int, callback: Closure(int): bool}
 */
function lessThan(int $expectedValue): array
{
    return ['expectedValue' => $expectedValue, 'callback' => static fn (int $value): bool => $value <  $expectedValue];
}

/**
 * @param int $expectedValue
 * @return array{expectedValue: int, callback: Closure(int): bool}
 */
function lessThanOrEqual(int $expectedValue): array
{
    return ['expectedValue' => $expectedValue, 'callback' => static fn (int $value): bool => $value <=  $expectedValue];
}

/**
 * @param int $expectedValue
 * @return array{expectedValue: int, callback: Closure(int): bool}
 */
function greaterThan(int $expectedValue): array
{
    return ['expectedValue' => $expectedValue, 'callback' => static fn (int $value): bool => $value >  $expectedValue];
}

/**
 * @param int $expectedValue
 * @return array{expectedValue: int, callback: Closure(int): bool}
 */
function greaterThanOrEqual(int $expectedValue): array
{
    return ['expectedValue' => $expectedValue, 'callback' => static fn (int $value): bool => $value >=  $expectedValue];
}
