<?php

namespace Kishron\ReleaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Revision
 *
 * @ORM\Table(name="revision")
 * @ORM\Entity(repositoryClass="Kishron\ReleaseBundle\Repository\RevisionRepository")
 */
class Revision
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
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;
    
    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="revisions")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;
    
    /**
     * @ORM\ManyToOne(targetEntity="DataCenter", inversedBy="revisions")
     * @ORM\JoinColumn(name="data_center_id", referencedColumnName="id")
     */
    private $dataCenter;
    
    /**
     * @ORM\ManyToOne(targetEntity="Story", inversedBy="revisions")
     * @ORM\JoinColumn(name="story_id", referencedColumnName="id")
     */
    private $story;


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
     * Set user
     *
     * @param \Kishron\ReleaseBundle\Entity\User $user
     * @return Revision
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
     * Set dataCenter
     *
     * @param \Kishron\ReleaseBundle\Entity\DataCenter $dataCenter
     * @return Revision
     */
    public function setDataCenter(\Kishron\ReleaseBundle\Entity\DataCenter $dataCenter = null)
    {
        $this->dataCenter = $dataCenter;

        return $this;
    }

    /**
     * Get dataCenter
     *
     * @return \Kishron\ReleaseBundle\Entity\DataCenter 
     */
    public function getDataCenter()
    {
        return $this->dataCenter;
    }

    /**
     * Set story
     *
     * @param \Kishron\ReleaseBundle\Entity\Story $story
     * @return Revision
     */
    public function setStory(\Kishron\ReleaseBundle\Entity\Story $story = null)
    {
        $this->story = $story;

        return $this;
    }

    /**
     * Get story
     *
     * @return \Kishron\ReleaseBundle\Entity\Story 
     */
    public function getStory()
    {
        return $this->story;
    }
}
