<?php

declare(strict_types=1);

namespace App\Command;

use App\Exceptions\AddressNotFoundException;
use App\Exceptions\InvalidDataException;
use App\Exceptions\InvalidJsonException;
use App\Factory\AddressFactoryInterface;
use App\Service\FileReaderInterface;
use App\Service\FileWriterInterface;
use App\Service\LocationInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

// The name of the command is what users type after "php bin/console"
#[AsCommand(
    name: 'calculate:distance',
    description: 'Calculates distances between addresses and a destination',
    aliases: ['calculate:distance']
)]
final class CalculateDistanceCommand extends Command
{
    public function __construct(
        private readonly LocationInterface $locationService,
        private readonly AddressFactoryInterface $addressFactory,
        private readonly FileReaderInterface $fileReader,
        private readonly FileWriterInterface $fileWriter,
        private readonly string $defaultJsonFilePath = 'files/addresses.json',
        private readonly string $csvFilePath = 'files/distances.csv'
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
            $output->writeln(messages: 'Distance calculation process has started.');

            $data = $this->fileReader->read(filePath: $filePath)->toArray();

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

    private function generateCsv(array $distances, OutputInterface $output): void
    {
        $this->fileWriter->write(fileName: $this->csvFilePath, data: $distances, headers: $this->getTableHeader());
        $output->writeln(messages: "[distances.csv] file has been generated.");
    }

    private function renderTable(OutputInterface $output, array $distances): void
    {
        $table = new Table(output: $output);
        $table->setHeaders(headers: $this->getTableHeader())
              ->setRows(rows: $distances)
              ->render();
    }

    private function getTableHeader(): array
    {
        return ['Sort Number', 'Distance', 'Name', 'Address'];
    }
}
