<?php

namespace App\Entity;

use App\Repository\BatchRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BookingRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Booking
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", nullable=true, unique=true)
     */
    private $googleId;

    /**
     * @ORM\Column(type="datetime")
     */
    private $start;

    /**
     * @ORM\Column(type="datetime")
     */
    private $end;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $location;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $recurrence;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $recurrenceFinishedOn;

    /**
     * @Assert\NotBlank()
     *
     * @ORM\Column(type="string", length=255)
     */
    private $summary;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $modifiedAt;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="bookings")
     */
    private $users;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Batch", inversedBy="bookings")
     */
    private $batches;

    /**
     * @return Batch|null
     */
    public function getBatches(): ?Batch
    {
        return $this->batches;
    }

    /**
     * @param Batch $batches
     */
    public function setBatches($batches): void
    {
        $this->batches = $batches;
    }

    /**
     * @return User|null
     */
    public function getUsers(): ?User
    {
        return $this->users;
    }

    /**
     * @param User $users
     */
    public function setUsers($users): void
    {
        $this->users = $users;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getRecurrenceFinishedOn(): ?\DateTimeInterface
    {
        return $this->recurrenceFinishedOn;
    }

    /**
     * @param mixed $recurrenceFinishedOn
     */
    public function setRecurrenceFinishedOn($recurrenceFinishedOn): void
    {
        $this->recurrenceFinishedOn = $recurrenceFinishedOn;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getModifiedAt(): \DateTimeInterface
    {
        return $this->modifiedAt;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getStart(): ?\DateTimeInterface
    {
        return $this->start;
    }

    /**
     * @param \DateTimeInterface $start
     *
     * @return $this
     */
    public function setStart(\DateTimeInterface $start): self
    {
        $this->start = $start;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getEnd(): ?\DateTimeInterface
    {
        return $this->end;
    }

    /**
     * @param \DateTimeInterface $end
     *
     * @return $this
     */
    public function setEnd(\DateTimeInterface $end): self
    {
        $this->end = $end;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     *
     * @return $this
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLocation(): ?string
    {
        return $this->location;
    }

    /**
     * @param string|null $location
     *
     * @return $this
     */
    public function setLocation(?string $location): self
    {
        $this->location = $location;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getRecurrence(): ?string
    {
        return $this->recurrence;
    }

    /**
     * @param string|null $recurrence
     *
     * @return $this
     */
    public function setRecurrence(?string $recurrence): self
    {
        $this->recurrence = $recurrence;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSummary(): ?string
    {
        return $this->summary;
    }

    /**
     * @param string $summary
     *
     * @return $this
     */
    public function setSummary(string $summary): self
    {
        $this->summary = $summary;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getGoogleId(): ?string
    {
        return $this->googleId;
    }

    /**
     * @param $googleId
     *
     * @return $this
     */
    public function setGoogleId($googleId): self
    {
        $this->googleId = $googleId;

        return $this;
    }

    /**
     * @ORM\PrePersist()
     */
    public function setCreatedAtValue(): void
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function setModifiedAtValue(): void
    {
        $this->modifiedAt = new \DateTime();
    }
}
