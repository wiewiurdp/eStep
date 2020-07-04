<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PresenceRepository")
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="presence_idx", columns={"booking_id", "attendee_id"})})
 */
class Presence
{
    /**
     *
     */
    public const INITIAL_STATED_VALUE = 'No response';
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Booking", inversedBy="presences" ,  cascade={"persist"})
     */
    private $booking;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Attendee", inversedBy="presences")
     */
    private $attendee;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $stated;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $actual;


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
    public function getStated(): ?string
    {
        return $this->stated;
    }

    /**
     * @param string|null $stated
     *
     * @return $this
     */
    public function setStated(?string $stated): self
    {
        $this->stated = $stated;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getActual(): ?bool
    {
        return $this->actual;
    }

    /**
     * @param bool|null $actual
     *
     * @return $this
     */
    public function setActual(?bool $actual): self
    {
        $this->actual = $actual;

        return $this;
    }

    /**
     * @return Booking|null
     */
    public function getBooking(): ?Booking
    {
        return $this->booking;
    }

    /**
     * @param Booking|null $booking
     *
     * @return $this
     */
    public function setBooking(?Booking $booking): self
    {
        $this->booking = $booking;

        return $this;
    }

    /**
     * @return Attendee|null
     */
    public function getAttendee(): ?Attendee
    {
        return $this->attendee;
    }

    /**
     * @param Attendee|null $attendee
     *
     * @return $this
     */
    public function setAttendee(?Attendee $attendee): self
    {
        $this->attendee = $attendee;

        return $this;
    }
}
