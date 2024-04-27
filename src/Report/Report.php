<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Report;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use TBoileau\PhpCodePolicyEnforcer\Expression\Expression;
use TBoileau\PhpCodePolicyEnforcer\Report\Enum\State;
use TBoileau\PhpCodePolicyEnforcer\Report\Enum\Status;
use TBoileau\PhpCodePolicyEnforcer\Report\Enum\Type;

/**
 * @implements IteratorAggregate<Report>
 * @implements ArrayAccess<int, Report>
 */
final class Report implements Countable, IteratorAggregate, ArrayAccess
{
    use NestedReportTrait;

    /**
     * @var Report[]
     */
    private array $children = [];

    private Status $status = Status::Created;

    public function __construct(private readonly mixed $value, private readonly Expression $expression)
    {
    }

    public function add(Report $report): void
    {
        $this->children[] = $report;
    }

    public function expression(): Expression
    {
        return $this->expression;
    }

    public function value(): mixed
    {
        return $this->value;
    }

    public function type(): Type
    {
        return Type::from($this->expression::class);
    }

    public function status(): Status
    {
        return $this->status;
    }

    public function is(Type $type): bool
    {
        return $type->equals($this->expression);
    }

    public function has(Status $status): bool
    {
        return $status->equals($this->status);
    }

    public function count(?Type $type = null, ?Status $status = null): int
    {
        return count($this->filter($type, $status));
    }

    /**
     * @return array<Report>
     */
    public function filter(?Type $type = null, ?Status $status = null): array
    {
        return array_filter(
            $this->flatten(),
            static fn (Report $report) => (
                ($type === null || $report->is($type))
                && ($status === null || $report->has($status))
            )
        );
    }

    /**
     * @return array<Report>
     */
    public function flatten(): array
    {
        $reports = [];

        foreach ($this->children as $child) {
            $reports[] = $child;
            $reports = array_merge($reports, $child->flatten());
        }

        return $reports;
    }

    public function setStatus(Status $status): void
    {
        $this->status = $status;
    }
}
