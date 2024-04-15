<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Expression;

use InvalidArgumentException;
use TBoileau\PhpCodePolicyEnforcer\Reflection\ReflectionClass;
use ReflectionClassConstant;
use ReflectionMethod;
use ReflectionParameter;
use ReflectionProperty;

enum Type
{
    case Classes;
    case Methods;
    case Parameters;
    case Properties;
    case Constants;


    public function str(mixed $value): string
    {
        if ($this === self::Classes) {
            return $this->validate($value, ReflectionClass::class)->getName();
        }

        if ($this === self::Methods) {
            $method = $this->validate($value, ReflectionMethod::class);
            return sprintf('%s::%s()', $method->getDeclaringClass()->getShortName(), $method->getName());
        }

        if ($this === self::Parameters) {
            $parameter = $this->validate($value, ReflectionParameter::class);
            return sprintf(
                '%s of %s',
                $parameter->getName(),
                $parameter->getDeclaringClass() !== null
                    ? sprintf('%s::%s', $parameter->getDeclaringClass()->getShortName(), $parameter->getDeclaringFunction()->getName())
                    : $parameter->getDeclaringFunction()->getName()
            );
        }

        if ($this === self::Properties) {
            $property = $this->validate($value, ReflectionProperty::class);
            return sprintf('%s::%s', $property->getDeclaringClass()->getName(), $property->getName());
        }

        $constant = $this->validate($value, ReflectionClassConstant::class);
        return sprintf('%s::%s', $constant->getDeclaringClass()->getName(), $constant->getName());
    }

    public function label(): string
    {
        return match ($this) {
            self::Classes => 'class',
            self::Methods => 'method',
            self::Parameters => 'parameter',
            self::Properties => 'property',
            self::Constants => 'constant',
        };
    }


    /**
     * @template T
     * @param class-string<T> $type
     * @return T
     */
    public function validate(mixed $value, mixed $type): mixed
    {
        $valid = $value instanceof $type;

        if (!$valid) {
            throw new InvalidArgumentException(sprintf('Expected a %s, got %s.', $this->label(), $type));
        }

        return $value;
    }
}
