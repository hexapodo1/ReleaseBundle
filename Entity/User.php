<?php

namespace Kishron\ReleaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="Kishron\ReleaseBundle\Repository\UserRepository")
 */
class User implements UserInterface, \Serializable
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;
    
    /**
     * @var boolean
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * @ORM\Column(name="salt", type="string", length=255)
     */
    protected $salt;
    
    /**
     * @var boolean
     * @ORM\Column(name="change_pass", type="boolean")
     */
    private $changePass;
    
    /**
     * @ORM\OneToMany(targetEntity="Revision", mappedBy="user")
     */
    private $revisions;
    
    /**
     * @ORM\OneToMany(targetEntity="UserRelease", mappedBy="user")
     */
    private $usersReleases;

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
     * Set name
     *
     * @param string $name
     * @return User
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
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->revisions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->usersReleases = new \Doctrine\Common\Collections\ArrayCollection();
        $this->isActive = true;
        // may not be needed, see section on salt below
        // $this->salt = md5(uniqid(null, true));
    }

    /**
     * Add revisions
     *
     * @param \Kishron\ReleaseBundle\Entity\Revision $revisions
     * @return User
     */
    public function addRevision(\Kishron\ReleaseBundle\Entity\Revision $revisions)
    {
        $this->revisions[] = $revisions;

        return $this;
    }

    /**
     * Remove revisions
     *
     * @param \Kishron\ReleaseBundle\Entity\Revision $revisions
     */
    public function removeRevision(\Kishron\ReleaseBundle\Entity\Revision $revisions)
    {
        $this->revisions->removeElement($revisions);
    }

    /**
     * Get revisions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRevisions()
    {
        return $this->revisions;
    }

    public function eraseCredentials() {
        
    }

    public function getRoles() {
        return array('ROLE_USER');
    }

    public function getUsername() {
        
    }

    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->name,
            $this->password,
            // see section on salt below
            // $this->salt,
        ));
    }

    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->name,
            $this->password,
            // see section on salt below
            // $this->salt
        ) = unserialize($serialized);
    }


    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return User
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean 
     */
    public function getIsActive()
    {
        return $this->isActive;
    }
    
    /**
     * Set salt
     *
     * @param string $salt
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    }
 
    /**
     * Get salt
     *
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Add usersRelease
     *
     * @param \Kishron\ReleaseBundle\Entity\UserRelease $usersRelease
     *
     * @return User
     */
    public function addUsersRelease(\Kishron\ReleaseBundle\Entity\UserRelease $usersRelease)
    {
        $this->usersReleases[] = $usersRelease;

        return $this;
    }

    /**
     * Remove usersRelease
     *
     * @param \Kishron\ReleaseBundle\Entity\UserRelease $usersRelease
     */
    public function removeUsersRelease(\Kishron\ReleaseBundle\Entity\UserRelease $usersRelease)
    {
        $this->usersReleases->removeElement($usersRelease);
    }

    /**
     * Get usersReleases
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsersReleases()
    {
        return $this->usersReleases;
    }
    

    /**
     * Set changePass
     *
     * @param boolean $changePass
     *
     * @return User
     */
    public function setChangePass($changePass)
    {
        $this->changePass = $changePass;

        return $this;
    }

    /**
     * Get changePass
     *
     * @return boolean
     */
    public function getChangePass()
    {
        return $this->changePass;
    }
}
