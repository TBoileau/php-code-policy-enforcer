<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Formatter;

use RuntimeException;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableCellStyle;
use Symfony\Component\Console\Helper\TableSeparator;
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

        $templating = new TwigTemplating('text');

        foreach ($report as $ruleReport) {
            $style->block($ruleReport->rule()->getReason(), 'RULE', 'fg=black;bg=cyan;options=bold', ' ', true);

            $style->section('Description');

            $style->writeln(
                sprintf(
                    "<fg=blue>%s</>",
                    $this->formatText($templating->render('rule.twig', ['rule' => $ruleReport->rule()]))
                )
            );

            $style->newLine();

            $style->section('Checks');

            $table = new Table($this->output);
            $table->setStyle('box');
            $table->setHeaders(['Class', 'Status']);

            foreach ($ruleReport->filter(State::Evaluated) as $valueReport) {
                $table->addRow([
                    $valueReport->value()->getName(),
                    new TableCell(
                        $valueReport->status()->value,
                        ['style' => new TableCellStyle(['align' => 'center'])]
                    )
                ]);
            }

            $table->render();

            $style->newLine();

            $violations = $ruleReport->filter(State::Evaluated, Status::Failed);

            if (count($violations) > 0) {
                $style->section('Violations');

                $maxWidth = max(0, ...array_map(fn ($value) => strlen($value->value()->getName()), $violations));

                foreach ($violations as $valueReport) {
                    $table = new Table($this->output);
                    $table->setStyle('box');
                    $table->setHeaders([
                        [
                            new TableCell(
                                $valueReport->value()->getName(),
                                [
                                    'style' => new TableCellStyle([
                                        'fg' => 'bright-blue',
                                    ]),
                                    'colspan' => 2,
                                ],
                            )],
                        ['Validator', 'Status']
                    ]);

                    $failedReports = $valueReport->should()->getFailedReports();

                    foreach (array_values($failedReports) as $k => $failedReport) {
                        $message = $this->formatText(
                            $templating->render(
                                'failed_report.twig',
                                ['report' => $failedReport]
                            )
                        );

                        if ($k > 0) {
                            $table->addRow(new TableSeparator());
                        }

                        $table->addRow([
                            $message,
                            new TableCell(
                                $failedReport->status()->value,
                                ['style' => new TableCellStyle(['align' => 'center'])]
                            )
                        ]);
                    }

                    $table->setColumnWidth(0, $maxWidth - 6);
                    $table->setColumnMaxWidth(1, 6);
                    $table->render();
                }
            }

            $style->newLine();

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

    private function formatText(string $text): string
    {
        return u($text)
            ->replaceMatches(
                '/(?<instruction>For each of |That |Should |which should |Because |  and |  or |  xor |  not )/',
                static fn (array $matches): string => sprintf("<fg=green;options=bold>%s</>", $matches['instruction'])
            )
            ->replaceMatches(
                '/"(?<values>[^"]*)"/',
                static fn (array $matches): string => sprintf("<fg=yellow;options=bold>%s</>", $matches['values'])
            )
            ->trim("\n")
            ->toString();
    }
}
