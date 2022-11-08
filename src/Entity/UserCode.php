<?php
/*
 * This file is part of the Reservation application project.
 *
 * https://github.com/anmoroz
 */

namespace App\Entity;

use App\Core\Entity\EntityInterface;
use App\Repository\UserCodeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use DateTime;

#[ORM\Entity(repositoryClass: UserCodeRepository::class)]
#[ORM\HasLifecycleCallbacks]
class UserCode implements EntityInterface
{
    public const TYPE_EMAIL_VERIFICATION = 1;
    public const TYPE_PASSWORD_RESET = 2;

    private const USER_CODE_VALID_TIME = 86400;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $type = null;

    #[ORM\ManyToOne(inversedBy: "userCodes", cascade: ["persist"])]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?User $user = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function setRandomNumberCode(): self
    {
        $this->code = (string) rand(100002, 999998);

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAtNow(): self
    {
        $this->createdAt = new \DateTime();

        return $this;
    }

    public function isValid(): bool
    {
        if (!$this->createdAt) {

            return false;
        }

        try {
            $diff = (new DateTime())->getTimestamp() - $this->createdAt->getTimestamp();
        } catch (\Exception) {

            return false;
        }

        return $diff < self::USER_CODE_VALID_TIME;
    }
}
