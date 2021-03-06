<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\HttpFoundation\File\File;

use Vich\UploaderBundle\Mapping\Annotation as Vich;

use JMS\Serializer\Annotation as JMS;

/**
 * Class representig an event in general
 *
 * @ORM\Entity(repositoryClass="EventRepository")
 * @ORM\Table(name="events")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"speed_dating" = "SpeedDatingEvent"})
 *
 * @Vich\Uploadable
 *
 */
abstract class Event
{
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\NotBlank(message="Nom invalide : vide")
     */
    private $name;

    /**
     * @var Location
     *
     * @ORM\ManyToOne(targetEntity="Location")
     */
    private $location;

    /**
     * @var integer
     *
     * @ORM\Column(name="max_people", type="integer")
     * @Assert\NotBlank(message="Nombre max de personnes vide")
     */
    private $maxPeople;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_start", type="datetime")
     * @Assert\NotBlank(message="Date de début invalide : vide")
     * @Assert\DateTime(message="Date invalide")
     */
    private $dateStart;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_end", type="datetime")
     * @Assert\NotBlank(message="Date de début invalide : vide")
     * @Assert\DateTime(message="Date invalide")
     */
    private $dateEnd;

    /**
     * @var string
     *
     * @ORM\Column(name="base_price", type="decimal", precision=5, scale=2)
     * @Assert\NotNull()
     */
    private $basePrice;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="User")
     *
     * @JMS\Exclude()
     */
    private $animators;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="User")
     * @ORM\JoinTable(name="events_participants")
     *
     * @JMS\Exclude()
     */
    private $participants;

    /**
     * @var string
     *
     * @ORM\Column(name="state", type="string", length=10)
     * @Assert\Choice(choices={"pending", "confirmed", "cancelled"}, message = "Choose a valid state")
     */
    private $state;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     * @Assert\NotBlank()
     */
    private $description;

    /**
     * @Vich\UploadableField(mapping="event_image", fileNameProperty="imagePath")
     *
     * @var File
     */
    private $imageFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @var string
     */
    private $imagePath;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     * @Assert\DateTime()
     *
     * @JMS\Exclude()
     */
    private $createdAt;

    /**
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     * @Assert\DateTime()
     *
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @var Confirmation
     *
     * @ORM\OneToOne(targetEntity="Confirmation")
     *
     * @JMS\Exclude()
     */
    private $confirmation;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     *
     * @JMS\Exclude()
     */
    private $createdBy;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     *
     * @JMS\Exclude()
     */
    private $modifiedBy;

    /**
     * Creates a new User
     */
    public function __construct()
    {
        $this->animators = new ArrayCollection();
        $this->participants = new ArrayCollection();
        $this->state = self::getStates()["pending"];
        $this->createdAt = new \DateTime("now");
    }

    /**
     * @Assert\Callback
     * @param ExecutionContextInterface $context
     */
    public function validate(ExecutionContextInterface $context)
    {
        if($this->dateEnd < $this->dateStart) {
            $context->buildViolation('La date de fin ne doit pas être précédente à la date de début')
                ->atPath('dateEnd')
                ->addViolation();
        }
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set maxPeople
     *
     * @param integer $maxPeople
     *
     * @return Event
     */
    public function setMaxPeople($maxPeople)
    {
        $this->maxPeople = $maxPeople;

        return $this;
    }

    /**
     * Get maxPeople
     *
     * @return integer
     */
    public function getMaxPeople()
    {
        return $this->maxPeople;
    }

    /**
     * Set dateStart
     *
     * @param \DateTime $dateStart
     *
     * @return Event
     */
    public function setDateStart($dateStart)
    {
        $this->dateStart = $dateStart;

        return $this;
    }

    /**
     * Get dateStart
     *
     * @return \DateTime
     */
    public function getDateStart()
    {
        return $this->dateStart;
    }

    /**
     * Set dateEnd
     *
     * @param \DateTime $dateEnd
     *
     * @return Event
     */
    public function setDateEnd($dateEnd)
    {
        $this->dateEnd = $dateEnd;

        return $this;
    }

    /**
     * Get dateEnd
     *
     * @return \DateTime
     */
    public function getDateEnd()
    {
        return $this->dateEnd;
    }

    /**
     * Set basePrice
     *
     * @param string $basePrice
     *
     * @return Event
     */
    public function setBasePrice($basePrice)
    {
        $this->basePrice = $basePrice;

        return $this;
    }

    /**
     * Get basePrice
     *
     * @return string
     */
    public function getBasePrice()
    {
        return $this->basePrice;
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

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Event
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set location
     *
     * @param \AppBundle\Entity\Location $location
     *
     * @return Event
     */
    public function setLocation(Location $location = null)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return \AppBundle\Entity\Location
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Add animator
     *
     * @param \AppBundle\Entity\User $animator
     *
     * @return Event
     */
    public function addAnimator(User $animator)
    {
        $this->animators[] = $animator;

        return $this;
    }

    /**
     * Remove animator
     *
     * @param \AppBundle\Entity\User $animator
     */
    public function removeAnimator(User $animator)
    {
        $this->animators->removeElement($animator);
    }

    /**
     * Get animators
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAnimators()
    {
        return $this->animators;
    }

    /**
     * Set confirmation
     *
     * @param \AppBundle\Entity\Confirmation $confirmation
     *
     * @return Event
     */
    public function setConfirmation(Confirmation $confirmation = null)
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
     * Set createdBy
     *
     * @param \AppBundle\Entity\User $createdBy
     *
     * @return Event
     */
    public function setCreatedBy(User $createdBy = null)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return \AppBundle\Entity\User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Event
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set modifiedBy
     *
     * @param \AppBundle\Entity\User $modifiedBy
     *
     * @return Event
     */
    public function setModifiedBy(User $modifiedBy = null)
    {
        $this->modifiedBy = $modifiedBy;

        return $this;
    }

    /**
     * Get modifiedBy
     *
     * @return \AppBundle\Entity\User
     */
    public function getModifiedBy()
    {
        return $this->modifiedBy;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Event
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Set imagePath
     *
     * @param string $imagePath
     *
     * @return Event
     */
    public function setImagePath($imagePath)
    {
        $this->imagePath = $imagePath;

        return $this;
    }

    /**
     * Get imagePath
     *
     * @return string
     */
    public function getImagePath()
    {
        return $this->imagePath;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime
     *
     * @ORM\PreUpdate()
     */
    public function setUpdatedAt()
    {
        $this->updatedAt = new \DateTime("now");
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $image
     */
    public function setImageFile(File $image = null)
    {
        $this->imageFile = $image;

        if ($image) {
            $this->updatedAt = new \DateTime('now');
        }
    }

    /**
     * @return File
     */
    public function getImageFile()
    {
        return $this->imageFile;
    }

    /**
     * Add participant
     *
     * @param \AppBundle\Entity\User $participant
     *
     * @return Event
     */
    public function addParticipant(User $participant)
    {
        $this->participants[] = $participant;

        return $this;
    }

    /**
     * Remove participant
     *
     * @param \AppBundle\Entity\User $participant
     */
    public function removeParticipant(User $participant)
    {
        $this->participants->removeElement($participant);
    }

    /**
     * Get participants
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getParticipants()
    {
        return $this->participants;
    }
}
