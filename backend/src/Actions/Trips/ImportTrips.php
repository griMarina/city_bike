<?php

declare(strict_types=1);

namespace Grimarina\CityBike\Actions\Trips;

use Grimarina\CityBike\http\{ErrorResponse, Request, Response, SuccessfulResponse};
use Grimarina\CityBike\Repositories\TripsRepository;
use Grimarina\CityBike\Exceptions\{ImportException, InvalidArgumentException};
use Grimarina\CityBike\Actions\ActionInterface;
use League\Csv\Reader;

class ImportTrips implements ActionInterface
{
    public function __construct(
        private string $filename,
        private TripsRepository $tripsRepository
    ) {
    }

    public function handle(Request $request): Response
    {
        if (pathinfo($this->filename, PATHINFO_EXTENSION) !== 'csv') {
            return new ErrorResponse('Invalid file extension. Only CSV files are allowed.');
        }

        try {
            $csv = Reader::createFromPath($this->filename);
            $csv->setHeaderOffset(0);
            $this->tripsRepository->importCsv($csv);
            return new SuccessfulResponse(['message' => 'CSV file imported successfully.']);
        } catch (ImportException $e) {
            return new ErrorResponse($e->getMessage());
        }
    }
}
