<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RoleRepository")
 */
class Role
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Batch", inversedBy="roles")
     */
    private $batch;

    /**
     * @ORM\ManyToMany(targetEntity="Attendee", inversedBy="roles")
     */
    private $attendees;

    /**
     * Role constructor.
     */
    public function __construct()
    {
        $this->attendees = new ArrayCollection();
    }

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
     * @return Batch|null
     */
    public function getBatch(): ?Batch
    {
        return $this->batch;
    }

    /**
     * @param Batch|null $batch
     *
     * @return $this
     */
    public function setBatch(?Batch $batch): self
    {
        $this->batch = $batch;

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
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function __toString()
    {
        return $this->name;
    }
}
