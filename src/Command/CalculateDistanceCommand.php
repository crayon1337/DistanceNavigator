<?php

declare(strict_types=1);

namespace App\Command;

use App\Exceptions\AddressNotFoundException;
use App\Exceptions\InvalidDataException;
use App\Exceptions\InvalidJsonException;
use App\Factory\AddressFactoryInterface;
use App\Helpers\FileHelper;
use App\Service\FileReaderInterface;
use App\Service\LocationInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

// The name of the command is what users type after "php bin/console"
#[AsCommand(
    name: 'calculate:distance',
    description: 'Calculates distances between addresses and a destination',
    aliases: ['calculate:distance']
)]
class CalculateDistanceCommand extends Command
{
    private string $defaultJsonFilePath = 'files/addresses.json';
    private string $csvFilePath = 'files/distances.csv';

    public function __construct(
        protected LocationInterface $locationService,
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
        $filePath = $input->getArgument(name: 'file') ?? $this->defaultJsonFilePath;

        try {
            $data = $this->fileReader->make(filePath: $filePath)->toArray();

            $destination = $this->addressFactory->make(data: $data['destination']);
            $addresses = $this->addressFactory->resolveAddresses(data: $data['addresses']);

            $distances = $this->locationService->getDistances(destinationAddress: $destination, addresses: $addresses);

            $this->renderTable(output: $output, distances: $distances);
            $this->generateCsv(distances: $distances, output: $output);
        } catch (AddressNotFoundException | FileNotFoundException | InvalidJsonException | InvalidDataException $exception) {
            $output->writeln(messages: $exception->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    private function generateCsv(array $distances, OutputInterface $output)
    {
        FileHelper::export(fileName: $this->csvFilePath, data: $distances, headers: $this->getTableHeader());
        $output->writeln("[distances.csv] file has been generated.");
    }

    private function renderTable(OutputInterface $output, array $distances)
    {
        $table = new Table($output);
        $table->setHeaders($this->getTableHeader())
              ->setRows($distances)
              ->render();
    }

    private function getTableHeader(): array
    {
        return ['Sort Number', 'Distance', 'Name', 'Address'];
    }
}
