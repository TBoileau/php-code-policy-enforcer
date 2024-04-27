<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Exception;

use Exception;

abstract class CodePolicyEnforcerException extends Exception
{
    /**
     * @param array<string, mixed> $context
     */
    protected function __construct(string $message = "", private readonly array $context = [])
    {
        parent::__construct($message);
    }

    /**
     * @return array<string, mixed>
     */
    public function getContext(): array
    {
        return $this->context;
    }
}
