<?php

declare(strict_types=1);

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__.'/../')
    ->exclude('vendor')
;

return (new PhpCsFixer\Config())
    ->setCacheFile(__DIR__.'/.php-cs-fixer.cache')
    ->setRules(['@PSR12' => true,])
    ->setFinder($finder)
;
