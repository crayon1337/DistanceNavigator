<?php

namespace App\Tests\Integration\Service;

use App\DTO\Address;
use App\Exceptions\AddressNotFoundException;
use App\Exceptions\InvalidDataException;
use App\Exceptions\InvalidJsonException;
use App\Factory\AddressFactoryInterface;
use App\Service\External\MapClient\PositionStack\PositionStackAPI;
use App\Service\External\MapClient\PositionStack\Query;
use App\Service\FileReaderInterface;
use App\Service\LocationInterface;
use App\Service\LocationService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class LocationServiceTest extends KernelTestCase
{
    protected MockHttpClient $httpClient;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp(); //
        $this->httpClient = $this->mockAddressesResponse();
    }

    /**
     * @throws AddressNotFoundException
     */
    public function testAddressInformationCanBeResolvedUsingAPI(): void
    {
        // Setup
        $query = new Query(
            id: 'Adchieve HQ',
            address: "Sint Janssingel 92, 5211 DA 's-Hertogenbosch, The Netherlands"
        );
        $mapClient = new PositionStackAPI($this->httpClient, 'accessKey');

        // Act
        $addressInformation = $mapClient->fetchGeoInformation(query: $query)->first();

        // Assert
        $this->assertNotNull($addressInformation->getLatitude());
        $this->assertNotNull($addressInformation->getLongitude());
        $this->assertEquals(51.6882, $addressInformation->getLatitude());
        $this->assertEquals(5.298532, $addressInformation->getLongitude());
        $this->assertEquals("Sint Janssingel 92, 's Hertogenbosch, NB, Netherlands", $addressInformation->getLabel());
    }

    /**
     * @throws InvalidJsonException
     * @throws InvalidDataException
     * @throws AddressNotFoundException
     * @throws Exception
     */
    public function testDistanceCanBeCalculatedBetweenPointsAndDestination(): void
    {
        // Setup
        /** @var FileReaderInterface $fileReader */
        $fileReader = $this->getContainer()->get(FileReaderInterface::class);
        /** @var AddressFactoryInterface $addressFactory */
        $addressFactory = $this->getContainer()->get(AddressFactoryInterface::class);

        $data = $fileReader->read(filePath: 'files/addresses.json')->toArray();
        $destination = $addressFactory->make($data['destination']);
        $addresses = $addressFactory->resolveAddresses($data['addresses']);

        $mapClientApi = new PositionStackAPI($this->httpClient, 'accessKey');
        /** @var LocationInterface $locationService */
        $locationService = new LocationService($mapClientApi);

        // Act
        $distances = $locationService->getDistances(destinationAddress: $destination, addresses: $addresses);

        // Assert
        $this->assertNotEmpty($distances);
        $this->assertCount(8, $distances);
        // Ensure the closest route will always be first.
        $this->assertEquals("62.58 km", $distances[0]['distance']);
        $this->assertEquals("Adchieve Rotterdam", $distances[0]['name']);
        $this->assertEquals("120.43 km", $distances[1]['distance']);
        $this->assertEquals("Eastern Enterprise B.V.", $distances[1]['name']);
        $this->assertEquals("Sherlock Holmes", $distances[2]['name']);
        $this->assertEquals("The Pope", $distances[3]['name']);
        $this->assertEquals("The Empire State Building", $distances[4]['name']);
        $this->assertEquals("The White House", $distances[5]['name']);
        $this->assertEquals("Eastern Enterprise", $distances[6]['name']);
        $this->assertEquals("Neverland", $distances[7]['name']);
        // Ensure CSV file has been generated and the distances are written in the desired order.
        $this->assertFileExists('files/distances.csv');
        $this->assertStringEqualsFile('files/distances.csv', '"Sort Number",Distance,Name,Address
1,"62.58 km","Adchieve Rotterdam","Weena 505, 3013, The Netherlands"
2,"120.43 km","Eastern Enterprise B.V.","Deldenerstraat 70, 7551AH Hengelo, The Netherlands"
3,"377.17 km","Sherlock Holmes","221B Baker St., London, United Kingdom"
4,"549.33 km","The Pope","Saint Martha House, 00120 Citta del Vaticano, Vatican City"
5,"5,914.78 km","The Empire State Building","350 Fifth Avenue, New York City, NY 10118"
6,"6,242.47 km","The White House","1600 Pennsylvania Avenue, Washington, D.C., USA"
7,"6,635.96 km","Eastern Enterprise","Office no 1 Ground Floor , Dada House , Inside dada silk mills compound, Udhana Main Rd, near Chhaydo Hospital, Surat, 394210, India"
8,"9,041.94 km",Neverland,"5225 Figueroa Mountain Road, Los Olivos, Calif. 93441, USA"
');
    }

    /**
     * @throws Exception
     */
    private function mockAddressesResponse(): MockHttpClient
    {
        $info = [
            'http_code' => 200,
            'response_headers' => ['Content-Type: application/json'],
        ];

        $responses = array_map(fn ($data) => new MockResponse(json_encode($data), $info), $this->getMockData());

        return new MockHttpClient($responses, 'http://api.positionstack.com');
    }

    /**
     * @throws Exception
     */
    private function getMockData(): array
    {
        /** @var FileReaderInterface $fileReader */
        $fileReader = $this->getContainer()->get(FileReaderInterface::class);
        $content = $fileReader->read('tests/Integration/Service/MockData.json')->content();

        return json_decode($content, true);
    }
}
