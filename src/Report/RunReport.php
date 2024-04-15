<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Report;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use TBoileau\PhpCodePolicyEnforcer\CodePolicy;
use TBoileau\PhpCodePolicyEnforcer\Report\Enum\Status;

/**
 * @implements IteratorAggregate<RuleReport>
 * @implements ArrayAccess<int, RuleReport>
 */
final class RunReport implements Countable, IteratorAggregate, ArrayAccess
{
    use CollectionTrait;

    /**
     * @var RuleReport[]
     */
    protected array $children = [];

    public function __construct(private readonly CodePolicy $codePolicy)
    {
    }

    public function codePolicy(): CodePolicy
    {
        return $this->codePolicy;
    }

    public function add(RuleReport $ruleReport): void
    {
        $this->children[] = $ruleReport;
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
}
