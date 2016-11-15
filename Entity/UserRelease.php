<?php

namespace Kishron\ReleaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserRelease
 *
 * @ORM\Table(name="user_release")
 * @ORM\Entity(repositoryClass="Kishron\ReleaseBundle\Repository\UserReleaseRepository")
 */
class UserRelease
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
     * @var bool
     *
     * @ORM\Column(name="owner", type="boolean")
     */
    private $owner;
    
    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="usersReleases")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="ReleaseObj", inversedBy="usersReleases")
     * @ORM\JoinColumn(name="release_id", referencedColumnName="id")
     */
    private $release;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set owner
     *
     * @param boolean $owner
     *
     * @return UserRelease
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return boolean
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Set user
     *
     * @param \Kishron\ReleaseBundle\Entity\User $user
     *
     * @return UserRelease
     */
    public function setUser(\Kishron\ReleaseBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Kishron\ReleaseBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set release
     *
     * @param \Kishron\ReleaseBundle\Entity\ReleaseObj $release
     *
     * @return UserRelease
     */
    public function setRelease(\Kishron\ReleaseBundle\Entity\ReleaseObj $release = null)
    {
        $this->release = $release;

        return $this;
    }

    /**
     * Get release
     *
     * @return \Kishron\ReleaseBundle\Entity\ReleaseObj
     */
    public function getRelease()
    {
        return $this->release;
    }
}
