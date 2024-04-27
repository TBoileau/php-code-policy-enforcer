<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Report;

use ArrayAccess;
use Closure;
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
    use NestedReportTrait;

    /**
     * @var ValueReport[]
     */
    private array $children = [];

    public function __construct(private readonly RunReport $runReport, private readonly Rule $rule, private readonly ?Closure $onHit)
    {
        foreach ($this->runReport->codePolicy()->classMap() as $class) {
            $this->children[] = new ValueReport($this, $class, $this->onHit);
        }
    }

    public function rule(): Rule
    {
        return $this->rule;
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

    public function run(): void
    {
        foreach ($this->children as $value) {
            $value->run();
        }
    }
}
