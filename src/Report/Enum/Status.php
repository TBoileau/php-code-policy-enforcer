<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Report\Enum;

enum Status: string
{
    case Succeeded = '✅';
    case Failed = '❌';
    case Created = '🆕';

    public function equals(Status $status): bool
    {
        return $this === $status;
    }

    public static function fromResult(bool $result): self
    {
        return match ($result) {
            true => self::Succeeded,
            false => self::Failed,
        };
    }
}
