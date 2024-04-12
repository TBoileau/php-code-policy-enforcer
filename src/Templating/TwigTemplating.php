<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Templating;

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\ArrayLoader;

final readonly class TwigTemplating implements Templating
{
    private Environment $twig;

    public function __construct()
    {
        $this->twig = new Environment(new ArrayLoader([]));
    }

    /**
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function render(string $template, array $context = []): string
    {
        return $this->twig->render($this->twig->createTemplate($template), $context);
    }
}
