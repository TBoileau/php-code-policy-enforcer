<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Lib\Helpers;

use TBoileau\PhpCodePolicyEnforcer\Expression\Expression;
use TBoileau\PhpCodePolicyEnforcer\Rule;

use function Symfony\Component\String\u;

function that(Expression ...$expressions): Rule
{
    return (new Rule())->that(...$expressions);
}

function str_intersect($str1, $str2)
{
    $str1 = u($str1)->ensureEnd('/')->toString();
    $str2 = u($str2)->ensureEnd('/')->toString();
    $length = min(strlen($str1), strlen($str2));
    $commonPrefix = "";

    for ($i = 0; $i < $length; $i++) {
        if ($str1[$i] != $str2[$i]) {
            break;
        }
        $commonPrefix .= $str1[$i];
    }

    $lastSlash = strrpos($commonPrefix, '/');
    $length = strlen($commonPrefix);


    if ($lastSlash !== false && $lastSlash !== $length - 1) {
        return substr($commonPrefix, 0, $lastSlash + 1);
    }

    return $commonPrefix;
}
