<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Report;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use TBoileau\PhpCodePolicyEnforcer\Expression\ConditionalExpression;
use TBoileau\PhpCodePolicyEnforcer\Expression\Expression;
use TBoileau\PhpCodePolicyEnforcer\Expression\LogicalExpression;
use TBoileau\PhpCodePolicyEnforcer\Expression\Operator;
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

    /**
     * @var Report[]
     */
    private ?array $trace = null;

    private Status $status = Status::Created;

    private ?Report $parent = null;

    public function __construct(private readonly mixed $value, private readonly Expression $expression)
    {
    }

    public function add(Report $report): void
    {
        $report->parent = $this;
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
     * @return Report[]
     */
    public function getFailedReports(): array
    {
        $reports = $this->filter(type: Type::Conditional, status: Status::Failed);

        usort($reports, static fn (Report $a, Report $b) => $a->expression()->getLevel() <=> $b->expression()->getLevel());

        /** @var Report[] $failedReports */
        $failedReports = [];

        $processed = count($reports);

        while ($processed > 0) {
            $report = array_shift($reports);
            if ($report->parent->is(Type::Logical)) {
                foreach ($report->parent->children as $sibling) {
                    if ($sibling === $report) {
                        continue;
                    }

                    if (
                        $sibling->is(Type::Logical)
                        && $sibling->expression->is(Operator::Or)
                        && $sibling->has(Status::Succeeded)
                    ) {
                        $processed -= count(
                            array_filter(
                                $reports,
                                static fn (Report $failedReport): bool => in_array(
                                    $failedReport,
                                    $sibling->flatten(),
                                    true
                                )
                            )
                        );

                        $reports = array_filter(
                            $reports,
                            static fn (Report $failedReport): bool => !in_array(
                                $failedReport,
                                $sibling->flatten(),
                                true
                            )
                        );
                    }
                }
            }

            $failedReports[] = $report;
            --$processed;
        }

        return $failedReports;
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

    /**
     * @return Report[]
     */
    public function getTrace(): array
    {
        if ($this->trace !== null) {
            return $this->trace;
        }

        $trace = [];

        $current = $this;

        while (null !== $current) {
            $trace[] = $current;
            $current = $current->parent;
        }

        $this->trace = $trace;

        return $this->trace;
    }
}
