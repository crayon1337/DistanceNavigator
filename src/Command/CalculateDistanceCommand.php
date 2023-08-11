<?php

namespace App\Command;

use App\Factory\AddressFactoryInterface;
use App\Helpers\FileHelper;
use App\Helpers\FileReaderInterface;
use App\Service\GeolocationInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

// The name of the command is what users type after "php bin/console"
#[AsCommand(
    name: 'calculate:distance',
    description: 'Calculates distances between addresses and a destination',
    aliases: ['calculate:distance']
)]
class CalculateDistanceCommand extends Command
{
    use LoggerAwareTrait;

    public function __construct(
        protected GeolocationInterface $geolocation,
        protected AddressFactoryInterface $addressFactory,
        protected FileReaderInterface $fileReader
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument(
                name: 'file',
                mode: InputArgument::REQUIRED,
                description: 'Path to JSON file that contains destination and adresses.'
            )
            ->setHelp(help: 'This command allows you to calculate distance between points and a destination. Results will be generated to a CSV file.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = $this->fileReader->make(filePath: $input->getArgument(name: 'file'));

        if (!$file->exists()) {
            $output->writeln('The specified JSON file does not exist.');
            return COMMAND::FAILURE;
        }

        $data = $file->toArray();

        $destination = $this->addressFactory->make(data: $data['destination']);
        $addresses = [];

        foreach ($data['addresses'] as $address) {
            $addresses[] = $this->addressFactory->make(data: $address);
        }

        $distances = $this->geolocation->getDistances(destinationAddress: $destination, locations: $addresses);

        if (!is_array($distances) || count($distances) === 1) {
            return COMMAND::FAILURE;
        }

        $this->generateCsv(fileName: 'distances.csv', distances: $distances, output: $output);
        $this->renderTable(output: $output, distances: $distances);

        return Command::SUCCESS;
    }

    private function generateCsv(string $fileName, array $distances, OutputInterface $output)
    {
        FileHelper::export($fileName, $distances);
        $output->writeln("[$fileName] file has been generated.");
    }

    private function renderTable(OutputInterface $output, array $distances)
    {
        $table = new Table($output);
        $table
            ->setHeaders(['Sort Number', 'From', 'To', 'Distance'])
            ->setRows($distances)
            ->render();
    }
}
