<?php
/*
 * This file is part of the Reservation application project.
 *
 * https://github.com/anmoroz
 */

declare(strict_types=1);


namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;
use DateTimeInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class ReservationDetails
{
    #[Assert\NotNull(message: "Дата заезда не заполнена", groups: ['close_reservation'])]
    private ?DateTimeInterface $checkin;

    #[Assert\NotNull(message: "Дата выезда не заполнена", groups: ['close_reservation'])]
    private ?DateTimeInterface $checkout;

    #[Assert\Sequentially([
        new Assert\NotBlank(message: "Введите количество взрослых"),
        new Assert\Type('integer', message: 'Количество взрослых должно быть числом'),
        new Assert\GreaterThan(0, message: 'Количество взрослых должно быть больше 0')
    ])]
    private ?int $adults;

    #[Assert\Sequentially([
        new Assert\NotBlank(message: "Введите количество детей"),
        new Assert\Type('integer', message: 'Количество детей должно быть числом'),
        new Assert\GreaterThanOrEqual(0, message: 'Количество детей должно быть больше или равно нулю')
    ])]
    private ?int $children;

    private ?ContactDetails $contactDetails = null;

    private string $note = '';

    /**
     * @param DateTimeInterface|null $checkin
     * @param DateTimeInterface|null $checkout
     * @param int|null $adults
     * @param int|null $children
     */
    public function __construct(?DateTimeInterface $checkin, ?DateTimeInterface $checkout, ?int $adults, ?int $children)
    {
        $this->checkin = $checkin;
        $this->checkout = $checkout;
        $this->adults = $adults;
        $this->children = $children;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getCheckin(): ?DateTimeInterface
    {
        return $this->checkin;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getCheckout(): ?DateTimeInterface
    {
        return $this->checkout;
    }

    /**
     * @return int|null
     */
    public function getAdults(): ?int
    {
        return $this->adults;
    }

    /**
     * @return int|null
     */
    public function getChildren(): ?int
    {
        return $this->children;
    }

    /**
     * @return ContactDetails|null
     */
    public function getContactDetails(): ?ContactDetails
    {
        return $this->contactDetails;
    }

    /**
     * @param ContactDetails|null $contactDetails
     * @return ReservationDetails
     */
    public function setContactDetails(?ContactDetails $contactDetails): ReservationDetails
    {
        $this->contactDetails = $contactDetails;
        return $this;
    }

    /**
     * @return string
     */
    public function getNote(): string
    {
        return $this->note;
    }

    /**
     * @param string $note
     */
    public function setNote(string $note): void
    {
        $this->note = $note;
    }

    #[Assert\Callback(groups: ['close_reservation'])]
    public function validate(ExecutionContextInterface $context, $payload)
    {
        if ($this->checkin >= $this->checkout) {
            $context->buildViolation('Дата заезда должна быть раньше даты выезда')
                ->atPath('checkin')
                ->addViolation();
        }
    }
}