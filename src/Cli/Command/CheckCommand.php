<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Cli\Command;

use InvalidArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TBoileau\PhpCodePolicyEnforcer\CodePolicy;
use TBoileau\PhpCodePolicyEnforcer\Formatter\ConsoleFormatter;
use TBoileau\PhpCodePolicyEnforcer\Runner;

class CheckCommand extends Command
{
    public function __construct()
    {
        parent::__construct('check');
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Check that architectural rules are matched.')
            ->setHelp('This command allows you check that architectural rules defined in your config file are matched.')
            ->addOption(
                'config',
                'c',
                InputOption::VALUE_OPTIONAL,
                'File containing configs, such as rules to be matched',
                'php-code-policy-enforcer.php'
            )
            ->addOption(
                'format',
                'f',
                InputOption::VALUE_OPTIONAL,
                'Format to use for output. Default is text.',
                'text'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $codePolicyFile */
        $codePolicyFile = $input->getOption('config');

        /** @var string $format */
        $format = $input->getOption('format');

        if (!file_exists($codePolicyFile)) {
            $output->writeln(sprintf('<error>Config file not found: %s</error>', $codePolicyFile));
            return Command::FAILURE;
        }

        /** @var CodePolicy $codePolicy  */
        $codePolicy = require $codePolicyFile;

        $io = new SymfonyStyle($input, $output);

        $io->title('PHP Code Policy Enforcer');

        $io->progressStart(count($codePolicy));

        $report = (new Runner($codePolicy))
            ->onHit(function () use ($io): void {
                $io->progressAdvance();
            })
            ->run();

        $io->progressFinish();

        $formatter = match ($format) {
            'text' => new ConsoleFormatter($input, $output),
            default => throw new InvalidArgumentException(sprintf('Unknown format: %s', $format))
        };

        $formatter->format($report);

        if (!$report->hasSucceeded()) {
            $io->error('Some violations found. Please fix them before commit.');
            return Command::FAILURE;
        }

        $io->success('No violations found. Well done !');
        return Command::SUCCESS;
    }
}
