<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Report;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use TBoileau\PhpCodePolicyEnforcer\Report\Enum\State;
use TBoileau\PhpCodePolicyEnforcer\Report\Enum\Status;
use TBoileau\PhpCodePolicyEnforcer\Rule;

/**
 * @implements IteratorAggregate<ValueReport>
 * @implements ArrayAccess<int, ValueReport>
 */
final class RuleReport implements Countable, IteratorAggregate, ArrayAccess
{
    use CollectionTrait;

    /**
     * @var ValueReport[]
     */
    protected array $children = [];

    public function __construct(private readonly Rule $rule)
    {
    }

    public function rule(): Rule
    {
        return $this->rule;
    }

    public function add(mixed $value): ValueReport
    {
        $valueReport = new ValueReport($this, $value);
        $this->children[] = $valueReport;

        return $valueReport;
    }

    public function count(?State $state = null, ?Status $status = null): int
    {
        return count($this->filter($state, $status));
    }

    /**
     * @return array<ValueReport>
     */
    public function filter(?State $state = null, ?Status $status = null): array
    {
        return array_filter(
            $this->children,
            static fn (ValueReport $report) => (
                ($state === null || $report->is($state))
                && ($status === null || $report->has($status))
            )
        );
    }

    public function status(): Status
    {
        foreach ($this->children as $value) {
            if ($value->is(State::Ignored)) {
                continue;
            }

            if ($value->should()->has(Status::Failed)) {
                return Status::Failed;
            }
        }

        return Status::Succeeded;
    }

    public function has(Status $status): bool
    {
        return $status->equals($this->status());
    }
}
