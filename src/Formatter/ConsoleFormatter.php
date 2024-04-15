<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Formatter;

use RuntimeException;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableCellStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TBoileau\PhpCodePolicyEnforcer\Report\Enum\State;
use TBoileau\PhpCodePolicyEnforcer\Report\Enum\Status;

use TBoileau\PhpCodePolicyEnforcer\Report\Enum\Type;
use TBoileau\PhpCodePolicyEnforcer\Report\Report;
use TBoileau\PhpCodePolicyEnforcer\Report\RunReport;
use TBoileau\PhpCodePolicyEnforcer\Templating\TwigTemplating;

use function Symfony\Component\String\u;

final readonly class ConsoleFormatter implements Formatter
{
    public function __construct(private InputInterface $input, private OutputInterface $output)
    {
    }

    public function format(RunReport $report): void
    {
        $style = new SymfonyStyle($this->input, $this->output);

        $templating = new TwigTemplating();

        foreach ($report as $ruleReport) {
            $style->block($ruleReport->rule()->reason(), 'RULE', 'fg=black;bg=cyan;options=bold', ' ', true);

            $style->section('Description');

            $style->writeln(
                sprintf(
                    "<fg=blue>%s</>",
                    u(implode("\n", $ruleReport->rule()->message($templating)))
                        ->replaceMatches(
                            '/(?<instruction>For each|That|Should|and |or |xor |not )/',
                            static fn (array $matches): string => sprintf("<fg=cyan;options=bold>%s</>", $matches['instruction'])
                        )
                )
            );

            $style->newLine();

            $table = new Table($this->output);
            $table->setStyle('box');
            $table->setHeaders([
                u($ruleReport->rule()->type()->label())->title()->toString(),
                'Status'
            ]);

            foreach ($ruleReport->filter(State::Evaluated) as $valueReport) {
                $table->addRow([
                    $ruleReport->rule()->type()->str($valueReport->value()),
                    new TableCell(
                        $valueReport->status()->value,
                        ['style' => new TableCellStyle(['align' => 'center'])]
                    )
                ]);
            }

            $style->section('Checks');

            $table->render();

            $style->newLine();

            $violations = $ruleReport->filter(State::Evaluated, Status::Failed);

            if (count($violations) > 0) {
                $style->section('Violations');

                foreach ($violations as $valueReport) {
                    $table = new Table($this->output);
                    $table->setStyle('box');
                    $table->setHeaders([
                        [new TableCell($ruleReport->rule()->type()->str($valueReport->value()), ['colspan' => 2])],
                        ['Validator', 'Status']
                    ]);
                    $table->setRows(array_map(
                        static fn (Report $report): array => [
                            $report->expression()->message($templating),
                            $report->status()->value,
                        ],
                        $valueReport->should()->filter(Type::Conditional),
                    ));
                    $table->render();
                    $style->newLine();
                }
            }

            $style->section('Reports');


            $style->definitionList(
                ['Parsed' => $ruleReport->count()],
                ['Ignored' => $ruleReport->count(State::Ignored)],
                ['Evaluated' => $ruleReport->count(State::Evaluated)],
                ['Succeeded' => $ruleReport->count(State::Evaluated, Status::Succeeded)],
                ['Failed' => $ruleReport->count(State::Evaluated, Status::Failed)],
            );

            if ($ruleReport->status() === Status::Succeeded) {
                $style->block('Rule passed', 'INFO', 'fg=black;bg=blue', ' ', true);
            } else {
                $style->warning(sprintf('Rule failed with %s violations(s)', $ruleReport->count(State::Evaluated, Status::Failed)));
            }

            $style->newLine();
        }
    }
}
