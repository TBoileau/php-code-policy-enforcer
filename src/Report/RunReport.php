<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Report;

use ArrayAccess;
use Closure;
use Countable;
use IteratorAggregate;
use TBoileau\PhpCodePolicyEnforcer\CodePolicy;
use TBoileau\PhpCodePolicyEnforcer\Report\Enum\Status;
use TBoileau\PhpCodePolicyEnforcer\Rule;

/**
 * @implements IteratorAggregate<RuleReport>
 * @implements ArrayAccess<int, RuleReport>
 */
final class RunReport implements Countable, IteratorAggregate, ArrayAccess
{
    use NestedReportTrait;

    /**
     * @var RuleReport[]
     */
    private array $children = [];

    public function __construct(private readonly CodePolicy $codePolicy)
    {
    }

    public function codePolicy(): CodePolicy
    {
        return $this->codePolicy;
    }

    public function add(Rule $rule, ?Closure $onHit): RuleReport
    {
        $ruleReport = new RuleReport($this, $rule, $onHit);
        $this->children[] = $ruleReport;
        return $ruleReport;
    }

    public function hasSucceeded(): bool
    {
        foreach ($this as $child) {
            if ($child->has(Status::Failed)) {
                return false;
            }
        }

        return true;
    }

    public function count(): int
    {
        return count($this->codePolicy);
    }
}
