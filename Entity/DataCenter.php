<?php

namespace Kishron\ReleaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DataCenter
 *
 * @ORM\Table(name="data_center")
 * @ORM\Entity(repositoryClass="Kishron\ReleaseBundle\Repository\DataCenterRepository")
 */
class DataCenter
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var bool
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active;

    /**
     * @ORM\OneToMany(targetEntity="Revision", mappedBy="dataCenter")
     */
    private $revisions;

    /**
     * @ORM\ManyToOne(targetEntity="HipchatRoom", inversedBy="dataCenters")
     * @ORM\JoinColumn(name="hipchatRoom_id", referencedColumnName="id")
     */
    private $hipchatRoom;

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
     * @return DataCenter
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
     * Constructor
     */
    public function __construct()
    {
        $this->revisions = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add revisions
     *
     * @param \Kishron\ReleaseBundle\Entity\Revision $revisions
     * @return DataCenter
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

    /**
     * Set active
     *
     * @param boolean $active
     * @return DataCenter
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
     * Set hipchatRoom
     *
     * @param \Kishron\ReleaseBundle\Entity\HipchatRoom $hipchatRoom
     * @return DataCenter
     */
    public function setHipchatRoom(\Kishron\ReleaseBundle\Entity\HipchatRoom $hipchatRoom = null)
    {
        $this->hipchatRoom = $hipchatRoom;

        return $this;
    }

    /**
     * Get hipchatRoom
     *
     * @return \Kishron\ReleaseBundle\Entity\HipchatRoom 
     */
    public function getHipchatRoom()
    {
        return $this->hipchatRoom;
    }
}
