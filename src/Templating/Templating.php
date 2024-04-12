<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Templating;

interface Templating
{
    /**
     * @param array<string, mixed> $context
     */
    public function render(string $template, array $context = []): string;
}
