<?php
/*
 * This file is part of the Reservation application project.
 *
 * https://github.com/anmoroz
 */

declare(strict_types=1);

namespace App\Entity;

use App\Core\Entity\EntityInterface;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use DateTimeInterface;
use DateTime;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\HasLifecycleCallbacks]
class User implements UserInterface, PasswordAuthenticatedUserInterface, EntityInterface
{
    public const ROLE_USER = 'ROLE_USER';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 12, unique: true, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(length: 90, unique: true)]
    private ?string $email = null;

    #[ORM\Column(type: Types::BOOLEAN, columnDefinition: "TINYINT(1) NOT NULL DEFAULT 0")]
    private bool $isVerified = false;

    #[ORM\Column(type: Types::BOOLEAN, columnDefinition: "TINYINT(1) NOT NULL DEFAULT 0")]
    private bool $isRegistrationCompleted = false;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private DateTimeInterface $credentialsUpdatedAt;

    #[ORM\Column(type: Types::JSON)]
    private array $roles = [];

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private string $note = '';

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Room::class, orphanRemoval: true)]
    private Collection $properties;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: RefreshToken::class, orphanRemoval: true)]
    private Collection $refreshTokens;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserCode::class, orphanRemoval: true, cascade: ["persist"])]
    private Collection $userCodes;

    public function __construct()
    {
        $this->properties = new ArrayCollection();
        $this->refreshTokens = new ArrayCollection();
        $this->userCodes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return bool
     */
    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    /**
     * @param bool $isVerified
     * @return User
     */
    public function setIsVerified(bool $isVerified): User
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * @return bool
     */
    public function isRegistrationCompleted(): bool
    {
        return $this->isRegistrationCompleted;
    }

    /**
     * @param bool $isRegistrationCompleted
     * @return User
     */
    public function setIsRegistrationCompleted(bool $isRegistrationCompleted): User
    {
        $this->isRegistrationCompleted = $isRegistrationCompleted;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return DateTimeInterface
     */
    public function getCredentialsUpdatedAt(): DateTimeInterface
    {
        return $this->credentialsUpdatedAt;
    }

    /**
     * @return $this
     */
    public function setCredentialsUpdatedAtNow(): self
    {
        $this->credentialsUpdatedAt = new DateTime();

        return $this;
    }

    /**
     * @param DateTimeInterface $credentialsUpdatedAt
     * @return $this
     */
    public function setCredentialsUpdatedAt(DateTimeInterface $credentialsUpdatedAt): self
    {
        $this->credentialsUpdatedAt = $credentialsUpdatedAt;

        return $this;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = self::ROLE_USER;

        return array_unique($roles);
    }

    /**
     * @param array $roles
     * @return $this
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

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

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(string $note): self
    {
        $this->note = $note;

        return $this;
    }

    /**
     * @return Collection<int, Room>
     */
    public function getProperties(): Collection
    {
        return $this->properties;
    }

    public function addProperty(Room $property): self
    {
        if (!$this->properties->contains($property)) {
            $this->properties->add($property);
            $property->setUser($this);
        }

        return $this;
    }

    public function removeProperty(Room $property): self
    {
        if ($this->properties->removeElement($property)) {
            // set the owning side to null (unless already changed)
            if ($property->getUser() === $this) {
                $property->setUser(null);
            }
        }

        return $this;
    }

    public function eraseCredentials()
    {

    }

    /**
     * Returns the identifier for this user (e.g. its username or email address).
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @return Collection<int, RefreshToken>
     */
    public function getRefreshTokens(): Collection
    {
        return $this->refreshTokens;
    }

    public function addRefreshToken(RefreshToken $refreshToken): self
    {
        if (!$this->refreshTokens->contains($refreshToken)) {
            $this->refreshTokens->add($refreshToken);
            $refreshToken->setUser($this);
        }

        return $this;
    }

    public function removeRefreshToken(RefreshToken $refreshToken): self
    {
        if ($this->refreshTokens->removeElement($refreshToken)) {
            // set the owning side to null (unless already changed)
            if ($refreshToken->getUser() === $this) {
                $refreshToken->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, UserCode>
     */
    public function getUserCodes(): Collection
    {
        return $this->userCodes;
    }

    public function addUserCode(UserCode $userCode): self
    {
        if (!$this->userCodes->contains($userCode)) {
            $this->userCodes->add($userCode);
            $userCode->setUser($this);
        }

        return $this;
    }

    public function removeUserCode(UserCode $userCode): self
    {
        if ($this->userCodes->removeElement($userCode)) {
            // set the owning side to null (unless already changed)
            if ($userCode->getUser() === $this) {
                $userCode->setUser(null);
            }
        }

        return $this;
    }
}
