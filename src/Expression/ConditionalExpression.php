<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Expression;

use Closure;
use TBoileau\PhpCodePolicyEnforcer\Exception\ExpressionException;
use TBoileau\PhpCodePolicyEnforcer\Report\Enum\Status;
use TBoileau\PhpCodePolicyEnforcer\Report\Report;

final class ConditionalExpression implements Expression
{
    use NestedExpressionTrait;

    private bool $not = false;

    /**
     * @param string $name
     * @param Closure(mixed): bool $validator
     * @param array<string, mixed> $parameters
     * @param string $message
     * @param Expression|null $childExpression
     * @param Closure|null $childValues
     * @throws ExpressionException
     */
    public function __construct(
        private readonly string  $name,
        private readonly Closure $validator,
        private readonly array   $parameters = [],
        private readonly string  $message = '',
        private readonly ?Expression $childExpression = null,
        private readonly ?Closure $childValues = null
    ) {
        if ($this->childExpression !== null) {
            if ($this->childValues === null) {
                throw ExpressionException::noChildValues($this);
            }

            $this->childExpression->setParent($this);
        }
    }

    public function evaluate(Report $report): bool
    {
        $result = !$this->not === ($this->validator)($report->value());

        $report->setStatus(Status::fromResult($result));

        if ($result && $this->childExpression !== null && $this->childValues !== null) {
            $childValues = ($this->childValues)($report->value());

            foreach ($childValues as $childValue) {
                $childReport = new Report($childValue, $this->childExpression);

                $result = $result && $this->childExpression->evaluate($childReport);

                $report->add($childReport);
            }
        }

        return $result;
    }

    public function isNot(): bool
    {
        return $this->not;
    }

    public function setNot(bool $not): void
    {
        $this->not = $not;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return array<string, mixed>
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function getChildExpression(): ?Expression
    {
        return $this->childExpression;
    }
}
