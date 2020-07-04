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
    /**
     * Batch constructor.
     */
    public function __construct()
    {
        $this->attendees = new ArrayCollection();
        $this->roles = new ArrayCollection();
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
     * @ORM\OneToMany(targetEntity="App\Entity\Role", mappedBy="batch")
     */
    private $roles;

    /**
     * @ORM\ManyToMany(targetEntity="Attendee", mappedBy="batches")
     */
    private $attendees;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getRoles(): Collection
    {
        return $this->roles;
    }

    /**
     * @param Role $role
     *
     * @return $this
     */
    public function addRole(Role $role): self
    {
        if (!$this->roles->contains($role)) {
            $this->roles[] = $role;
            $role->setBatch($this);
        }

        return $this;
    }

    /**
     * @param Role $role
     *
     * @return $this
     */
    public function removeRole(Role $role): self
    {
        if ($this->roles->contains($role)) {
            $this->roles->removeElement($role);
            // set the owning side to null (unless already changed)
            if ($role->getBatch() === $this) {
                $role->setBatch(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Attendee[]
     */
    public function getAttendees(): Collection
    {
        return $this->attendees;
    }

    /**
     * @param Attendee $attendee
     *
     * @return $this
     */
    public function addAttendee(Attendee $attendee): self
    {
        if (!$this->attendees->contains($attendee)) {
            $this->attendees[] = $attendee;
            $attendee->addBatch($this);
        }

        return $this;
    }

    /**
     * @param Attendee $attendee
     *
     * @return $this
     */
    public function removeAttendee(Attendee $attendee): self
    {
        if ($this->attendees->contains($attendee)) {
            $this->attendees->removeElement($attendee);
            $attendee->removeBatch($this);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->name;
    }
}
