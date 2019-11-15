<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BatchRepository")
 */
class Batch
{
    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->roles = new ArrayCollection();
        $this->bookings = new ArrayCollection();
    }

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
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="batches")
     */
    private $users;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $roles;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Booking", inversedBy="batches")
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

    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $users): self
    {
        $this->users = $users;

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

    public function getBookings(): ?int
    {
        return $this->bookings;
    }

    public function setBookings(?int $bookings): self
    {
        $this->bookings = $bookings;

        return $this;
    }
}
