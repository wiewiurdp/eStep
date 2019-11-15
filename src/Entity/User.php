<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $surname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $mail;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Batch", inversedBy="users")
     */
    private $batches;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $roles;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Booking", inversedBy="users")
     */
    private $bookings;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): self
    {
        $this->mail = $mail;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return Batch|null
     */
    public function getBatches(): ?Batch
    {
        return $this->batches;
    }

    /**
     * @param Batch $batches
     *
     * @return $this
     */
    public function setBatches(Batch $batches): self
    {
        $this->batches = $batches;

        return $this;
    }

    public function getRoles(): ?int
    {
        return $this->roles;
    }

    public function setRoles(?int $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @return Booking|null
     */
    public function getBookings(): ?Booking
    {
        return $this->bookings;
    }

    /**
     * @param Booking $bookings
     *
     * @return $this
     */
    public function setBookings(Booking $bookings): self
    {
        $this->bookings = $bookings;

        return $this;
    }
}
