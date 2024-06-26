<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Evaluator;

use Closure;
use TBoileau\PhpCodePolicyEnforcer\Evaluator\Strategy\AndEvaluator;
use TBoileau\PhpCodePolicyEnforcer\Evaluator\Strategy\NotEvaluator;
use TBoileau\PhpCodePolicyEnforcer\Evaluator\Strategy\OrEvaluator;
use TBoileau\PhpCodePolicyEnforcer\Evaluator\Strategy\XorEvaluator;
use TBoileau\PhpCodePolicyEnforcer\Expression\Expression;
use TBoileau\PhpCodePolicyEnforcer\Expression\LogicalExpression;
use TBoileau\PhpCodePolicyEnforcer\Expression\Operator;
use TBoileau\PhpCodePolicyEnforcer\Report\Report;

final class Evaluator
{
    /**
     * @var array<string, Strategy>
     */
    private static array $strategies = [];

    public static function evaluate(LogicalExpression $expression, Report $report): bool
    {
        if (count(self::$strategies) === 0) {
            self::initialize();
        }

        return self::$strategies[$expression->getOperator()->name]->evaluate($expression, $report);
    }

    private static function initialize(): void
    {
        self::$strategies[Operator::And->name] = new AndEvaluator();
        self::$strategies[Operator::Or->name] = new OrEvaluator();
        self::$strategies[Operator::Xor->name] = new XorEvaluator();
    }
}
