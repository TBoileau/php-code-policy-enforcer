<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Report;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use TBoileau\PhpCodePolicyEnforcer\Report\Enum\Status;
use TBoileau\PhpCodePolicyEnforcer\Rule;
use TBoileau\PhpCodePolicyEnforcer\RuleSet;

/**
 * @implements IteratorAggregate<RuleReport>
 * @implements ArrayAccess<int, RuleReport>
 */
final class RuleSetReport implements Countable, IteratorAggregate, ArrayAccess
{
    use CollectionTrait;

    /**
     * @var RuleReport[]
     */
    protected array $children = [];

    public function __construct(private readonly RuleSet $ruleSet)
    {
    }

    public function ruleSet(): RuleSet
    {
        return $this->ruleSet;
    }

    public function count(): int
    {
        return $this->ruleSet->count();
    }

    public function add(Rule $rule): RuleReport
    {
        $ruleReport = new RuleReport($rule);
        $this->children[] = $ruleReport;

        return $ruleReport;
    }

    public function has(Status $status): bool
    {
        foreach ($this->children as $child) {
            if (!$child->has($status)) {
                return false;
            }
        }

        return true;
    }
}
