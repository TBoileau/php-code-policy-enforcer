<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

final class CompileCommand extends Command
{
    public function __construct()
    {
        parent::__construct('compile');
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Executes the cross-compilation bash script.')
            ->setHelp('This command allows you to execute the bash script for cross-compiling Go applications...');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $scriptPath = __DIR__ . '/../../bin/cross_compile.sh';

        $process = new Process(['bash', $scriptPath]);
        $process->setWorkingDirectory(__DIR__ . '/../../scripts/');
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $output->write($process->getOutput());

        return Command::SUCCESS;
    }
}
