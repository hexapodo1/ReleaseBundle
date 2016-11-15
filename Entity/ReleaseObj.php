<?php

namespace Kishron\ReleaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ReleaseObj
 *
 * @ORM\Table(name="release_obj")
 * @ORM\Entity(repositoryClass="Kishron\ReleaseBundle\Repository\ReleaseObjRepository")
 */
class ReleaseObj
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
     * @ORM\Column(name="code", type="string", length=50)
     */
    private $code;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var bool
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active;
    
    /**
     * @var string
     *
     * @ORM\Column(name="objectID", type="string", length=50)
     */
    private $objectID;
    
    /**
     * @var string
     *
     * @ORM\Column(name="objectUUID", type="string", length=50)
     */
    private $objectUUID;

    /**
     * @var bool
     *
     * @ORM\Column(name="success", type="boolean")
     */
    private $success;
    
    /**
     * @ORM\OneToMany(targetEntity="Story", mappedBy="release")
     */
    private $stories;
    
    /**
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="releases")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     */
    private $project;
    
    /**
     * @ORM\OneToMany(targetEntity="UserRelease", mappedBy="release")
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
     * Set code
     *
     * @param string $code
     * @return ReleaseObj
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return ReleaseObj
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
     * Set active
     *
     * @param boolean $active
     * @return ReleaseObj
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean 
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set success
     *
     * @param boolean $success
     * @return ReleaseObj
     */
    public function setSuccess($success)
    {
        $this->success = $success;

        return $this;
    }

    /**
     * Get success
     *
     * @return boolean 
     */
    public function getSuccess()
    {
        return $this->success;
    }
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->stories = new \Doctrine\Common\Collections\ArrayCollection();
        $this->usersReleases = new \Doctrine\Common\Collections\ArrayCollection();
    }
    /**
     * Add stories
     *
     * @param \Kishron\ReleaseBundle\Entity\Story $stories
     * @return ReleaseObj
     */
    public function addStory(\Kishron\ReleaseBundle\Entity\Story $stories)
    {
        $this->stories[] = $stories;

        return $this;
    }

    /**
     * Remove stories
     *
     * @param \Kishron\ReleaseBundle\Entity\Story $stories
     */
    public function removeStory(\Kishron\ReleaseBundle\Entity\Story $stories)
    {
        $this->stories->removeElement($stories);
    }

    /**
     * Get stories
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getStories()
    {
        return $this->stories;
    }
    
    public function __toString() {
        return $this->getCode(); // . " - " . $this->getDate()->format('M d, Y');
    }

    /**
     * Set objectUUID
     *
     * @param string $objectUUID
     * @return ReleaseObj
     */
    public function setObjectUUID($objectUUID)
    {
        $this->objectUUID = $objectUUID;

        return $this;
    }

    /**
     * Get objectUUID
     *
     * @return string 
     */
    public function getObjectUUID()
    {
        return $this->objectUUID;
    }

    /**
     * Set objectID
     *
     * @param string $objectID
     * @return ReleaseObj
     */
    public function setObjectID($objectID)
    {
        $this->objectID = $objectID;

        return $this;
    }

    /**
     * Get objectID
     *
     * @return string 
     */
    public function getObjectID()
    {
        return $this->objectID;
    }

    /**
     * Set project
     *
     * @param \Kishron\ReleaseBundle\Entity\Project $project
     * @return ReleaseObj
     */
    public function setProject(\Kishron\ReleaseBundle\Entity\Project $project = null)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get project
     *
     * @return \Kishron\ReleaseBundle\Entity\Project 
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Add usersRelease
     *
     * @param \Kishron\ReleaseBundle\Entity\UserRelease $usersRelease
     *
     * @return ReleaseObj
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

}
