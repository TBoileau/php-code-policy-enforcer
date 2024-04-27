<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Templating;

use TBoileau\PhpCodePolicyEnforcer\Templating\Twig\CodePolicyEnforcerExtension;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extension\StringLoaderExtension;
use Twig\Extra\String\StringExtension;
use Twig\Loader\FilesystemLoader;

final readonly class TwigTemplating implements Templating
{
    private Environment $twig;

    public function __construct(string $format)
    {
        $loader = new FilesystemLoader(__DIR__ . '/../../templates/' . $format);
        $this->twig = new Environment($loader);
        $this->twig->addExtension(new StringExtension());
        $this->twig->addExtension(new StringLoaderExtension());
        $this->twig->addExtension(new CodePolicyEnforcerExtension());
    }

    /**
     * @param array<string, mixed> $context
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function render(string $template, array $context = []): string
    {
        return $this->twig->render($template, $context);
    }
}
