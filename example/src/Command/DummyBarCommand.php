<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DummyBarCommand extends Command
{
    protected static $defaultName = 'dummy:bar';
    protected static $defaultDescription = 'this is a demo command';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->success('dummy:bar is done');

        return Command::SUCCESS;
    }
}
