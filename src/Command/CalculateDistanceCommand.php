<?php

namespace App\Command;

use App\Service\External\PositionStackAPI;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

// The name of the command is what users type after "php bin/console"
#[AsCommand(name: 'calculate:distance')]
class CalculateDistanceCommand extends Command
{
    public function __construct(protected PositionStackAPI $geoLocator)
    {
        $this->geoLocator = $geoLocator;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $result = $this->geoLocator->getForward("Adchieve HQ - Sint Janssingel 92, 5211 DA 's-Hertogenbosch, The Netherlands");

        var_dump($result);

        return Command::SUCCESS;
    }
}
