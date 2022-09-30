<?php
/*
 * This file is part of the Reservation application project.
 *
 * https://github.com/anmoroz
 */

declare(strict_types=1);


namespace App\Service;

use App\Core\Exception\ValidationException;
use App\Core\Model\PaginatedRequestConfiguration;
use App\Core\Service\Pagination\PaginatorInterface;
use App\Entity\Reservation;
use App\Entity\Contact;
use App\Entity\Room;
use App\Model\ReservationDetails;
use App\Repository\ReservationRepository;
use App\Repository\ContactRepository;

class ReservationService
{
    public function __construct(
        private ReservationRepository $reservationRepository,
        private ContactRepository $contactRepository,
        private ContactService $contactService
    )
    {
    }

    /**
     * @param int $id
     * @param Room $room
     * @return Reservation|null
     */
    public function findReservationById(int $id, Room $room): ?Reservation
    {
        return $this->reservationRepository->findOneBy(['id' => $id, 'room' => $room]);
    }

    /**
     * @param Reservation $reservation
     * @return void
     */
    public function delete(Reservation $reservation): void
    {
        $this->reservationRepository->remove($reservation, true);
    }

    /**
     * @param Room $room
     * @param Reservation $reservation
     * @param ReservationDetails $reservationDetails
     */
    public function update(Room $room, Reservation $reservation, ReservationDetails $reservationDetails): void
    {
        $this->checkExistsReservation(
            $room,
            $reservationDetails->getCheckin(),
            $reservationDetails->getCheckout(),
            $reservation
        );

        $contactDetails = $reservationDetails->getContactDetails();
        if ($reservationDetails->getAdults() > 0 && is_null($contactDetails)) {
            throw new ValidationException('Не указано контактное лицо');
        }

        if ($contactDetails) {
            $contact = $this->contactRepository->findOneByPhone($contactDetails->getPhone());
            if (!$contact) {
                throw new ValidationException('Контактное лицо с таким телефоном не найдено');
            }

            $contact
                ->setPhone($contactDetails->getPhone())
                ->setName($contactDetails->getName());
        }


        $this->updateReservationAttributes($reservation, $reservationDetails, $contact ?? null);

        $this->reservationRepository->add($reservation, true);
    }

    /**
     * @param Room $room
     * @param ReservationDetails $reservationDetails
     * @return Reservation
     */
    public function create(Room $room, ReservationDetails $reservationDetails): Reservation
    {
        $this->checkExistsReservation(
            $room,
            $reservationDetails->getCheckin(),
            $reservationDetails->getCheckout()
        );

        $contact = null;
        $contactDetails = $reservationDetails->getContactDetails();
        if (!is_null($contactDetails)) {
            $contact = $this->contactRepository->findOneByPhone($contactDetails->getPhone());
            if (!$contact) {
                $contact = $this->contactService->create($contactDetails);
            }

            $contact
                ->setPhone($contactDetails->getPhone())
                ->setName($contactDetails->getName())
                ->setNote($contactDetails->getNote());
        }

        $reservation = new Reservation();
        $reservation
            ->setRoom($room)
            ->setCreatedAtNow();

        $this->updateReservationAttributes($reservation, $reservationDetails, $contact);

        $this->reservationRepository->add($reservation, true);

        return $reservation;
    }

    /**
     * @param $reservation
     * @param ReservationDetails $reservationDetails
     * @param Contact|null $contact
     * @return void
     */
    private function updateReservationAttributes(
        $reservation,
        ReservationDetails $reservationDetails,
        ?Contact $contact
    ): void
    {
        if ($contact) {
            $reservation->setContact($contact);
        }

        $reservation
            ->setCheckin($reservationDetails->getCheckin())
            ->setCheckout($reservationDetails->getCheckout())
            ->setAdults($contact ? $reservationDetails->getAdults() : 0)
            ->setChildren($contact ? $reservationDetails->getChildren() : 0)
            ->setNote($reservationDetails->getNote());
    }

    public function checkExistsReservation(
        Room $room,
        \DateTimeInterface $checkin,
        \DateTimeInterface $checkout,
        ?Reservation $reservation = null
    ): void
    {
        // end_date > start_search && start_date < end_search
        $queryBuilder = $this->reservationRepository->createQueryBuilderWithMainAlias();
        $queryBuilder
            ->where($queryBuilder->expr()->eq('o.room', ':room'))
            ->andWhere($queryBuilder->expr()->gt('o.checkout', ':checkin'))
            ->andWhere($queryBuilder->expr()->lt('o.checkin', ':checkout'))
            ->setParameters([
                'room' => $room,
                'checkin' => $checkin,
                'checkout' => $checkout
            ]);

        if ($reservation) {
            $queryBuilder
                ->andWhere('o.id <> :reservationId')
                ->setParameter('reservationId', $reservation->getId());
        }

        /** @var Reservation $reservation */
        $reservation = $queryBuilder->getQuery()->getOneOrNullResult();

        if ($reservation) {
            if (is_null($reservation->getContact())) {
                throw new ValidationException('Бронирование закрыто на эти даты');
            }

            throw new ValidationException(sprintf(
                'Пересечение дат бронирования (заезд %s %s)',
                $reservation->getCheckin()->format('d.m.Y'),
                $reservation->getContact()->getName()
            ));
        }
    }

    /**
     * @param Room $room
     * @param PaginatedRequestConfiguration $paginatedRequest
     * @return PaginatorInterface
     */
    public function findAll(
        Room                          $room,
        PaginatedRequestConfiguration $paginatedRequest
    ): PaginatorInterface
    {
        $queryBuilder = $this->reservationRepository->createQueryBuilderWithMainAlias();

        $paginatedRequest->addCriteria('room', $room);

        $criteria = $paginatedRequest->getCriteria();
        if (isset($criteria['from'])) {
            $queryBuilder
                ->andWhere('o.checkin >= :from OR o.checkout >= :from')
                ->setParameter('from', $criteria['from']);
        }
        if (isset($criteria['to'])) {
            $queryBuilder
                ->andWhere('o.checkin <= :to OR o.checkout <= :to')
                ->setParameter('to', $criteria['to']);
        }

        return  $this->reservationRepository->createPaginator(
            $paginatedRequest->getPage(),
            $paginatedRequest->getCriteria(),
            $paginatedRequest->getSorting(),
            $queryBuilder
        );
    }
}