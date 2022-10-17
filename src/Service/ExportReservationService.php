<?php
/*
 * This file is part of the Booking application project.
 *
 * https://github.com/anmoroz
 */

declare(strict_types=1);


namespace App\Service;

use App\Core\Model\PaginatedRequestConfiguration;
use App\Entity\Reservation;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Writer\WriterInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\PropertyAccess\PropertyAccess;

class ExportReservationService
{
    const FORMAT = 'xlsx';
    const MIME_TYPE = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';

    const COLUMNS = [
        'checkin' => 'Прибытие',
        'checkout' => 'Выезд',
        'adults' => 'Взрослых',
        'children' => 'Детей',
        'contact.phone' => 'Телефон',
        'contact.name' => 'Имя',
        'note' => 'Примечание',
        'room.name' => 'Объект размещения'
    ];

    private ?File $exportFile = null;

    private WriterInterface $writer;

    public function __construct(
        private ReservationService $reservationService,
        private TemporaryFileStorage $temporaryFileStorage
    )
    {
        $this->writer = WriterEntityFactory::createXLSXWriter();
    }


    public function export(PaginatedRequestConfiguration $requestConfiguration): void
    {
        $this->createExportFile();
        $this->writer->openToFile($this->exportFile->getRealPath());

        $this->writeHeader();

        $this->writeRowsByRequestConfiguration($requestConfiguration);

        $this->writer->close();
    }

    private function writeRowsByRequestConfiguration(PaginatedRequestConfiguration $requestConfiguration): void
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        $paginator = $this->reservationService->findAllByPaginatedRequest($requestConfiguration);
        /** @var Reservation $reservation */
        foreach ($paginator->getItems() as $reservation) {
            $cells = [];
            foreach (self::COLUMNS as $propertyPath => $colName) {
                $cellValue = $propertyAccessor->getValue($reservation, $propertyPath);
                if ($cellValue instanceof \DateTime) {
                    $cellValue = $cellValue->format('d.m.Y');
                }
                array_push($cells, WriterEntityFactory::createCell($cellValue));
            }
            $this->writer->addRow(WriterEntityFactory::createRow($cells));
        }
    }

    private function writeHeader(): void
    {
        $cells = [];
        foreach (self::COLUMNS as $cellValue) {
            array_push($cells, WriterEntityFactory::createCell($cellValue));
        }

        $headerRow = WriterEntityFactory::createRow($cells);
        $this->writer->addRow($headerRow);
    }

    public function getExportFile(): ?File
    {
        return $this->exportFile;
    }

    private function createExportFile(): void
    {
        $fileName = sprintf('%s.%s', uniqid(), self::FORMAT);
        $this->exportFile = $this->temporaryFileStorage->create($fileName);
    }
}