<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Templating\Twig;

use Countable;
use Symfony\Component\String\Inflector\EnglishInflector;
use TBoileau\PhpCodePolicyEnforcer\Expression\ConditionalExpression;
use TBoileau\PhpCodePolicyEnforcer\Expression\Expression;
use TBoileau\PhpCodePolicyEnforcer\Expression\LogicalExpression;
use TBoileau\PhpCodePolicyEnforcer\Expression\Operator;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

use function Symfony\Component\String\u;

final class CodePolicyEnforcerExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('inflect', [$this, 'inflect']),
            new TwigFilter('quote', [$this, 'quote']),
            new TwigFilter('conditional', [$this, 'conditional']),
            new TwigFilter('template', [$this, 'template']),
            new TwigFilter('repeat', [$this, 'repeat']),
            new TwigFilter('sanitize', [$this, 'sanitize']),
        ];
    }

    public function sanitize(string $str): string
    {
        return u($str)->replaceMatches('/^\s*\n/m', '')->toString();
    }

    /**
     * @param string[] $words
     * @return string[]
     */
    public function quote(array $words): array
    {
        return array_map(
            static fn (string $word): string => u($word)
                ->ensureStart('"')
                ->ensureEnd('"')
                ->toString(),
            $words
        );
    }

    public function repeat(string $str, int|Expression $timesOrExpression): string
    {
        if (is_int($timesOrExpression)) {
            return str_repeat($str, $timesOrExpression);
        }

        return str_repeat($str, $timesOrExpression->getLevel() + 1);
    }

    public function template(Expression $expression): string
    {
        return $expression instanceof LogicalExpression ? 'expressions/logical.twig' : 'expressions/conditional.twig';
    }

    /**
     * @param int|Countable|mixed[] $count
     */
    public function inflect(string $word, int|Countable|array $count): string
    {
        $inflector = new EnglishInflector();

        $count = is_int($count) ? $count : count($count);

        $result = 1 >= $count ? $inflector->singularize($word) : $inflector->pluralize($word);

        return count($result) === 0 ? $word : $result[0];
    }

    public function conditional(string $str, Expression $expression, bool $infinitive): string
    {
        $isNot = $expression instanceof ConditionalExpression && $expression->isNot();

        if (!$isNot && !$infinitive) {
            return $str;
        }

        /** @var array<array{string, array<string, array{bool, bool}>}> $replaces */
        $replaces = [
            ['is', 'is not' => [true, false], 'be' => [false, true], 'not be' => [true, true]],
            ['has', 'has not' => [true, false], 'have' => [false, true], 'not have' => [true, true]],
            ['contains', 'does not contain' => [true, false], 'contain' => [false, true], 'not contain' => [true, true]],
            ['depends', 'does not depend' => [true, false], 'depend' => [false, true], 'not depend' => [true, true]],
            ['uses', 'does not use' => [true, false], 'use' => [false, true], 'not use' => [true, true]],
            ['implements', 'does not implement' => [true, false], 'implement' => [false, true], 'not implement' => [true, true]],
            ['extends', 'does not extend' => [true, false], 'extend' => [false, true], 'not extend' => [true, true]],
            ['matches', 'does not match' => [true, false], 'match' => [false, true], 'not match' => [true, true]],
            ['resides', 'does not reside' => [true, false], 'reside' => [false, true], 'not reside' => [true, true]]
        ];
        $str = u($str);
        foreach ($replaces as $replace) {
            $word = array_shift($replace);
            /** @var string $replaceBy */
            $replaceBy = array_search([$isNot, $infinitive], $replace, true);
            $str = $str->replace($word, $replaceBy);
        }
        return $str->toString();
    }
}
