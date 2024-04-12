<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Report;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use TBoileau\PhpCodePolicyEnforcer\CodePolicy;
use TBoileau\PhpCodePolicyEnforcer\Report\Enum\Status;
use TBoileau\PhpCodePolicyEnforcer\RuleSet;

/**
 * @implements IteratorAggregate<RuleSetReport>
 * @implements ArrayAccess<int, RuleSetReport>
 */
final class RunReport implements Countable, IteratorAggregate, ArrayAccess
{
    use CollectionTrait;

    /**
     * @var RuleSetReport[]
     */
    protected array $children = [];

    public function __construct(private readonly CodePolicy $codePolicy)
    {
    }

    public function codePolicy(): CodePolicy
    {
        return $this->codePolicy;
    }

    public function add(RuleSet $ruleSet): RuleSetReport
    {
        $ruleSetReport = new RuleSetReport($ruleSet);
        $this->children[] = $ruleSetReport;

        return $ruleSetReport;
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
