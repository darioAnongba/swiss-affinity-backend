<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 23.10.2015
 * Time: 20:32
 */

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class User representing a User (client or contributor)
 *
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User extends BaseUser
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
     * @ORM\Column(name="last_name", type="string", length=255)
     * @Assert\NotBlank(message="Nom invalide : vide", groups={"Registration", "Profile"})
     * @Assert\Regex(
     *        message="Nom invalide, max 50 caract., lettres uniquement",
     *        pattern="/^[a-zA-Z\s]{2,50}/",
     *        groups={"Registration", "Profile"}
     * )
     */
    protected $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=255)
     * @Assert\NotBlank(message="Prénom invalide : vide", groups={"Registration", "Profile"})
     * @Assert\Regex(
     *        message="Prénom invalide, max 50 caract., lettres uniquement",
     *        pattern="/^[a-zA-Z]{2,50}/",
     *        groups={"Registration", "Profile"})
     */
    protected $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="mobile_phone", type="string", length=18, nullable=true)
     * @Assert\Regex(
     *        message="Téléphone portable invalide",
     *        pattern="/^[+0-9\s]{10,18}/",
     *        groups={"Registration", "Profile"})
     */
    protected $mobilePhone;

    /**
     * @var string
     *
     * @ORM\Column(name="home_phone", type="string", length=18, nullable=TRUE)
     * @Assert\Regex(
     *        message="Téléphone fixe invalide",
     *        pattern="/^[+0-9\s]{10,18}/",
     *        groups={"Profile"})
     */
    protected $homePhone;

    /**
     * @var string
     *
     * @ORM\Column(name="gender", type="string", length=10)
     * @Assert\NotBlank(message="Sexe invalide : vide")
     * @Assert\Choice(
     *      choices = {"male", "female"},
     *      message = "Le choix du sexe n'est pas valide.",
     *      groups={"Registration", "Profile"})
     */
    protected $gender;

    /**
     * @var string
     *
     * @ORM\Column(name="relationship", type="string", length=10, nullable=true)
     * @Assert\Choice(
     *          choices={"single", "married", "divorced"},
     *          message = "Choisissez une relation valide.",
     *          groups={"Profile"})
     */
    protected $relationship;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="birth_date", type="date")
     * @Assert\NotBlank(message="Date de naissance invalide : vide", groups={"Registration", "Profile"})
     * @Assert\Date(message="Cette valeur n'est pas une date valide.", groups={"Registration", "Profile"})
     */
    protected $birthDate;

    /**
     * @var string
     *
     * @ORM\Column(name="profession", type="string", length=255, nullable=true)
     * @Assert\Regex(
     *        message="Prénom invalide, max 50 caract., lettres uniquement",
     *        pattern="/^[a-zA-Z]{2,100}/",
     *        groups={"Profile"})
     */
    protected $profession;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Location")
     */
    protected $locationsOfInterest;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Event")
     */

    protected $eventsAttended;
    /**
     * @var Address
     *
     * @ORM\OneToOne(targetEntity="Address", cascade={"persist", "remove"})
     */
    protected $address;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="UserCategory")
     */
    protected $categories;

    /**
     * Creates a new User
     */
    public function __construct()
    {
        parent::__construct();

        $this->locationsOfInterest = new ArrayCollection();
        $this->eventsAttended = new ArrayCollection();
        $this->categories = new ArrayCollection();
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     *
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    public function fullName()
    {
        return $this->getFirstName().' '.$this->getLastName();
    }

    /**
     * Set mobilePhone
     *
     * @param string $mobilePhone
     *
     * @return User
     */
    public function setMobilePhone($mobilePhone)
    {
        $this->mobilePhone = $mobilePhone;

        return $this;
    }

    /**
     * Get mobilePhone
     *
     * @return string
     */
    public function getMobilePhone()
    {
        return $this->mobilePhone;
    }

    /**
     * Set homePhone
     *
     * @param string $homePhone
     *
     * @return User
     */
    public function setHomePhone($homePhone)
    {
        $this->homePhone = $homePhone;

        return $this;
    }

    /**
     * Get homePhone
     *
     * @return string
     */
    public function getHomePhone()
    {
        return $this->homePhone;
    }

    /**
     * Set address
     *
     * @param \AppBundle\Entity\Address $address
     *
     * @return User
     */
    public function setAddress(Address $address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return \AppBundle\Entity\Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Add category
     *
     * @param UserCategory $category
     *
     * @return User
     */
    public function addCategory(UserCategory $category)
    {
        $this->categories[] = $category;

        return $this;
    }

    /**
     * Remove category
     *
     * @param UserCategory $category
     */
    public function removeCategory(UserCategory $category)
    {
        $this->categories->removeElement($category);
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Set gender
     *
     * @param boolean $gender
     *
     * @return User
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return boolean
     */
    public function getGender()
    {
        return $this->gender;
    }

    public static function getGenders() {
        return array('male' => 'Male', 'female' => 'Female');
    }

    /**
     * Set relationship
     *
     * @param boolean $relationship
     *
     * @return User
     */
    public function setRelationship($relationship)
    {
        $this->relationship = $relationship;

        return $this;
    }

    /**
     * Get relationship
     *
     * @return boolean
     */
    public function getRelationship()
    {
        return $this->relationship;
    }

    /**
     * Get list of possible relationships
     *
     * @return array
     */
    public static function getRelationships() {
        return array('single' => 'Single', 'divorced' => 'Divorced', 'married' => 'Married');
    }

    /**
     * Set birthDate
     *
     * @param \DateTime $birthDate
     *
     * @return User
     */
    public function setBirthDate($birthDate)
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    /**
     * Get birthDate
     *
     * @return \DateTime
     */
    public function getBirthDate()
    {
        return $this->birthDate;
    }

    /**
     * Set profession
     *
     * @param string $profession
     *
     * @return User
     */
    public function setProfession($profession)
    {
        $this->profession = $profession;

        return $this;
    }

    /**
     * Get profession
     *
     * @return string
     */
    public function getProfession()
    {
        return $this->profession;
    }

    /**
     * Add locationsOfInterest
     *
     * @param \AppBundle\Entity\Location $locationsOfInterest
     *
     * @return User
     */
    public function addLocationsOfInterest(Location $locationsOfInterest)
    {
        $this->locationsOfInterest[] = $locationsOfInterest;

        return $this;
    }

    /**
     * Remove locationsOfInterest
     *
     * @param \AppBundle\Entity\Location $locationsOfInterest
     */
    public function removeLocationsOfInterest(Location $locationsOfInterest)
    {
        $this->locationsOfInterest->removeElement($locationsOfInterest);
    }

    /**
     * Get locationsOfInterest
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLocationsOfInterest()
    {
        return $this->locationsOfInterest;
    }

    /**
     * Add eventsAttended
     *
     * @param \AppBundle\Entity\Event $eventsAttended
     *
     * @return User
     */
    public function addEventsAttended(Event $eventsAttended)
    {
        $this->eventsAttended[] = $eventsAttended;

        return $this;
    }

    /**
     * Remove eventsAttended
     *
     * @param \AppBundle\Entity\Event $eventsAttended
     */
    public function removeEventsAttended(Event $eventsAttended)
    {
        $this->eventsAttended->removeElement($eventsAttended);
    }

    /**
     * Get eventsAttended
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEventsAttended()
    {
        return $this->eventsAttended;
    }
}
