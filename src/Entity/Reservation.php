<?php
/*
 * This file is part of the Reservation application project.
 *
 * https://github.com/anmoroz
 */

declare(strict_types=1);

namespace App\Entity;

use App\Core\Entity\EntityInterface;
use App\Repository\ReservationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use DateTimeInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
#[ORM\Index(name: "RESERVATION_CHECKIN_IDX", columns: ["checkin"])]
#[ORM\Index(name: "RESERVATION_CHECKOUT_IDX", columns: ["checkout"])]
class Reservation implements EntityInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["reservation.show", "reservation.list", "contact.list"])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["contact.list"])]
    private ?Room $room = null;

    #[ORM\ManyToOne(inversedBy: 'reservations', cascade: ["persist"])]
    #[Groups(["reservation.show", "reservation.list"])]
    private ?Contact $contact = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(["reservation.show", "reservation.list", "contact.list"])]
    private ?DateTimeInterface $checkin = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(["reservation.show", "reservation.list", "contact.list"])]
    private ?DateTimeInterface $checkout = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(["reservation.show", "reservation.list", "contact.list"])]
    private ?string $note = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    #[Groups(["reservation.show", "reservation.list", "contact.list"])]
    private ?int $adults = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    #[Groups(["reservation.show", "reservation.list", "contact.list"])]
    private ?int $children = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(["reservation.show", "reservation.list"])]
    private ?DateTimeInterface $createdAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContact(): ?Contact
    {
        return $this->contact;
    }

    public function setContact(?Contact $contact): Reservation
    {
        $this->contact = $contact;

        return $this;
    }

    public function getRoom(): ?Room
    {
        return $this->room;
    }

    public function setRoom(?Room $room): self
    {
        $this->room = $room;

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getCheckin(): ?DateTimeInterface
    {
        return $this->checkin;
    }

    public function setCheckin(?DateTimeInterface $checkin): self
    {
        $this->checkin = $checkin;

        return $this;
    }

    public function getCheckout(): ?DateTimeInterface
    {
        return $this->checkout;
    }

    public function setCheckout(?DateTimeInterface $checkout): self
    {
        $this->checkout = $checkout;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(string $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getAdults(): ?int
    {
        return $this->adults;
    }

    public function setAdults(?int $adults): self
    {
        $this->adults = $adults;

        return $this;
    }

    public function getChildren(): ?int
    {
        return $this->children;
    }

    public function setChildren(?int $children): self
    {
        $this->children = $children;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAtNow(): self
    {
        $this->createdAt = new \DateTime();

        return $this;
    }

    public function setCreatedAt(DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
