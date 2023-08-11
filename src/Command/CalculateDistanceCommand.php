<?php

namespace App\Command;

use App\Factory\AddressFactoryInterface;
use App\Helpers\FileHelper;
use App\Service\FileReaderInterface;
use App\Service\GeolocationInterface;
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
                mode: InputArgument::OPTIONAL,
                description: 'Path to JSON file that contains destination and adresses.'
            )
            ->setHelp(help: 'This command allows you to calculate distance between points and a destination. Results will be generated to a CSV file.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filePath = $input->getArgument(name: 'file') ?? 'addresses.json';
        $file = $this->fileReader->make(filePath: $filePath);

        if (!$file->exists()) {
            $output->writeln('The specified JSON file does not exist.');
            return COMMAND::INVALID;
        }

        $data = $file->toArray();

        if (is_null($data)) {
            $output->writeln(messages: 'Error decoding JSON');
            return COMMAND::FAILURE;
        }

        if (!isset($data['destination']) && !isset($data['addresses'])) {
            $output->writeln(messages: 'We were unable to detect addresses and destination in the file you provided.');
            return COMMAND::FAILURE;
        }

        $destination = $this->addressFactory->make(data: $data['destination']);
        $addresses = [];

        foreach ($data['addresses'] as $address) {
            $addresses[] = $this->addressFactory->make(data: $address);
        }

        $distances = $this->geolocation->getDistances(destinationAddress: $destination, addresses: $addresses);

        if (empty($distances)) {
            return COMMAND::FAILURE;
        }

        $this->generateCsv(distances: $distances, output: $output);
        $this->renderTable(output: $output, distances: $distances);

        return Command::SUCCESS;
    }

    private function generateCsv(array $distances, OutputInterface $output)
    {
        FileHelper::export(fileName: 'distances.csv', data: $distances, headers: $this->getTableHeader());
        $output->writeln("[distances.csv] file has been generated.");
    }

    private function renderTable(OutputInterface $output, array $distances)
    {
        $table = new Table($output);
        $table
            ->setHeaders($this->getTableHeader())
            ->setRows($distances)
            ->render();
    }

    private function getTableHeader(): array
    {
        return ['Sort Number', 'Distance', 'Name', 'Address'];
    }
}
