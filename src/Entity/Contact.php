<?php
/*
 * This file is part of the Reservation application project.
 *
 * https://github.com/anmoroz
 */

declare(strict_types=1);

namespace App\Entity;

use App\Core\Entity\EntityInterface;
use App\Repository\ContactRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use DateTimeInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ContactRepository::class)]
#[ORM\Index(name: "CONTACT_PHONE_IDX", columns: ["phone"])]
#[ORM\Index(name: "CONTACT_NAME_IDX", columns: ["name"])]
#[ORM\Index(name: "CONTACT_PHONE_USER_IDX", columns: ["phone", "user_id"])]
class Contact implements EntityInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["contact.show", "contact.list"])]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 12)]
    #[Groups(["contact.show", "contact.list"])]
    private ?string $phone = null;

    #[ORM\Column(length: 255)]
    #[Groups(["contact.show", "contact.list"])]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(["contact.show", "contact.list"])]
    private string $note = '';

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(["contact.show", "contact.list"])]
    private ?DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    #[Groups(["contact.show", "list"])]
    private bool $isBanned = false;

    #[ORM\OneToMany(mappedBy: 'contact', targetEntity: Reservation::class)]
    private Collection $reservations;

    #[Groups(["contact.list"])]
    private ?Reservation $lastReservation = null;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): Contact
    {
        $this->user = $user;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getNote(): string
    {
        return $this->note;
    }

    public function setNote(string $note): self
    {
        $this->note = $note;

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

    public function isBanned(): bool
    {
        return $this->isBanned;
    }

    public function setIsBanned(bool $isBanned): self
    {
        $this->isBanned = $isBanned;

        return $this;
    }

    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): self
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations->add($reservation);
            $reservation->setContact($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getContact() === $this) {
                $reservation->setContact(null);
            }
        }

        return $this;
    }

    /**
     * @return Reservation|null
     */
    public function getLastReservation(): ?Reservation
    {
        $lastReservation = $this->reservations->last();
        if ($lastReservation) {
            $this->lastReservation = $lastReservation;
        }

        return $this->lastReservation;
    }

    /**
     * @param Reservation|null $lastReservation
     */
    public function setLastReservation(?Reservation $lastReservation): void
    {
        $this->lastReservation = $lastReservation;
    }
}
