<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer;

use Closure;
use LogicException;
use TBoileau\PhpCodePolicyEnforcer\ClassMapper\ClassMap;
use TBoileau\PhpCodePolicyEnforcer\Expression\Expression;
use TBoileau\PhpCodePolicyEnforcer\Expression\LogicalExpression;
use TBoileau\PhpCodePolicyEnforcer\Expression\Type;
use TBoileau\PhpCodePolicyEnforcer\Report\Enum\State;
use TBoileau\PhpCodePolicyEnforcer\Report\RuleSetReport;
use TBoileau\PhpCodePolicyEnforcer\Templating\Templating;

use function Symfony\Component\String\u;

final class Rule
{
    private ?LogicalExpression $that = null;

    private ?LogicalExpression $should = null;

    private ?string $reason = null;

    private ?ClassMap $classMap = null;

    /**
     * @param Type $type
     */
    private function __construct(private readonly Type $type)
    {
    }

    public function init(ClassMap $classMap): void
    {
        $this->classMap = $classMap;
    }

    public function type(): Type
    {
        return $this->type;
    }

    public static function classes(): Rule
    {
        return new self(Type::Classes);
    }

    public function that(Expression ...$expressions): Rule
    {
        $this->that = new LogicalExpression();

        foreach ($expressions as $expression) {
            $this->that->add($expression);
        }

        return $this;
    }

    public function should(Expression ...$expressions): Rule
    {
        $this->should = new LogicalExpression();

        foreach ($expressions as $expression) {
            $this->should->add($expression);
        }

        return $this;
    }

    public function because(string $reason): self
    {
        $this->reason = $reason;

        return $this;
    }

    public function reason(): string
    {
        if (null === $this->reason) {
            throw new LogicException('You must provide a reason');
        }

        return $this->reason;
    }

    public function getThat(): LogicalExpression
    {
        if (null === $this->that) {
            throw new LogicException('You must provide a filter');
        }

        return $this->that;
    }

    public function getShould(): LogicalExpression
    {
        if (null === $this->should) {
            throw new LogicException('You must provide an evaluator');
        }

        return $this->should;
    }

    public function check(RuleSetReport $ruleSetReport, ?Closure $onHit): void
    {
        if (null === $this->classMap) {
            throw new LogicException('You must provide a class map');
        }

        $ruleReport = $ruleSetReport->add($this);

        foreach ($this->classMap as $class) {
            $valueReport = $ruleReport->add($class);

            if ($onHit !== null) {
                $onHit->call($valueReport);
            }

            $this->getThat()->evaluate($class);

            if ($valueReport->state()->equals(State::Ignored)) {
                continue;
            }

            $this->getShould()->evaluate($class);
        }
    }

    /**
     * @return string[]
     */
    public function message(Templating $templating): array
    {
        $messages = [sprintf("For each %s", u($this->type()->label())->lower())];

        $this->appendMessages($messages, $this->getThat()->message($templating), "That");
        $this->appendMessages($messages, $this->getShould()->message($templating), "Should");

        return $messages;
    }

    /**
     * @param string[] $messages
     * @param string[]|string $childMessages
     */
    private function appendMessages(array &$messages, array | string $childMessages, string $prefix): void
    {
        if (is_string($childMessages)) {
            $messages[] = sprintf("%s %s", $prefix, $childMessages);
        } else {
            foreach ($childMessages as $k => $message) {
                if ($k === 0) {
                    $messages[] = sprintf("%s %s", $prefix, $message);
                } else {
                    $messages[] = sprintf("%s", $message);
                }
            }
        }
    }
}
