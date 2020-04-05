<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Service\SequenceHelper;
use Symfony\Component\Console\Helper\Table;

class SequenceMaxCommand extends Command
{
    protected static $defaultName = 'sequence:max';

    private $sequenceHelper;

    public function __construct(SequenceHelper $sequenceHelper)
    {
        $this->sequenceHelper = $sequenceHelper;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Find corresponding maximum value from sequence for given input');
        for($i = 1; $i <= 10; $i++)
        {
            $this->addArgument('arg'.$i, InputArgument::OPTIONAL, 'Input number'.$i);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('arg1');

        $arg = [];
        for($i = 1; $i <= 10; $i++)
        {
            if (is_int((int)$input->getArgument('arg'.$i))) {
                $arg[] = $input->getArgument('arg'.$i);
            }
        }
        $filtered = array_filter($arg);

        $table = new Table($output);
        $table->setHeaders(array('Input', 'Output'));
        $rows = [];
        foreach ($filtered as $item) {
            $rows[] = [$item, $this->sequenceHelper->getMaxSeguence($item)];
        }
        $table->setRows($rows);
        $table->render();

        return 0;
    }
}
