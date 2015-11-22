<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use JMS\Serializer\Annotation as JMS;

/**
 * Registration
 *
 * @ORM\Table(name="registrations")
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class Registration
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Event
     *
     * @ORM\ManyToOne(targetEntity="Event")
     */
    private $event;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @JMS\Exclude()
     */
    private $user;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     * @Assert\NotBlank(message="Date invalide : vide")
     * @Assert\DateTime(message="Entrez une date valide")
     */
    private $date;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modified_at", type="datetime", nullable=true)
     * @Assert\DateTime(message="Date non valide")
     */
    private $modifiedAt;

    /**
     * @var Confirmation
     *
     * @ORM\OneToOne(targetEntity="Confirmation", cascade={"remove"})
     *
     * @JMS\Exclude()
     */
    private $confirmation;

    /**
     * @var string
     *
     * @ORM\Column(name="state", type="string", length=10)
     * @Assert\Choice(choices={"pending", "confirmed", "cancelled", "deleted"}, message = "Choose a valid status.")
     */
    private $state;

    public function __construct(Event $event = null, User $user = null)
    {
        $this->date = new \DateTime('now');
        $this->state = 'pending';
        $this->event = $event;
        $this->user = $user;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get price
     *
     * @return string
     */
    public function getPrice()
    {
        return $this->event->getBasePrice();
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Registration
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set modifiedAt
     *
     * @return Registration
     *
     * @ORM\PreUpdate()
     */
    public function setModifiedAt()
    {
        $this->modifiedAt = new \DateTime('now');
    }

    /**
     * Get modifiedAt
     *
     * @return \DateTime
     */
    public function getModifiedAt()
    {
        return $this->modifiedAt;
    }

    /**
     * Set event
     *
     * @param \AppBundle\Entity\Event $event
     *
     * @return Registration
     */
    public function setEvent(\AppBundle\Entity\Event $event = null)
    {
        $this->event = $event;

        return $this;
    }

    /**
     * Get event
     *
     * @return \AppBundle\Entity\Event
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Registration
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set confirmation
     *
     * @param \AppBundle\Entity\Confirmation $confirmation
     *
     * @return Registration
     */
    public function setConfirmation(\AppBundle\Entity\Confirmation $confirmation = null)
    {
        $this->confirmation = $confirmation;

        return $this;
    }

    /**
     * Get confirmation
     *
     * @return \AppBundle\Entity\Confirmation
     */
    public function getConfirmation()
    {
        return $this->confirmation;
    }

    /**
     * Set state
     *
     * @param string $state
     *
     * @return Event
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    public static function getStates() {
        return array('pending' => 'Pending', 'confirmed' => 'Confirmed', 'cancelled' => 'Cancelled');
    }
}
