<?php

namespace App\Command;

use App\Services\InitService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class InitCommand extends Command
{
    protected static $defaultName = 'z:app:init';

    /** @var InitService  */
    private $initService;

    /**
     * InitCommand constructor.
     * @param InitService $initService
     */
    public function __construct(InitService $initService)
    {
        $this->initService  = $initService;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('z:app:init');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->syncAppTimezones();
        $io->success('syncAppTimezones');

        return Command::SUCCESS;
    }

    public function syncAppTimezones():void
    {
        $this->initService->init();
    }

}
