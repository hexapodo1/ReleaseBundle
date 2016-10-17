<?php

namespace Kishron\ReleaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Project
 *
 * @ORM\Table(name="project")
 * @ORM\Entity(repositoryClass="Kishron\ReleaseBundle\Repository\ProjectRepository")
 */
class Project
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
     * @ORM\Column(name="name", type="string", length=100)
     */
    private $name;

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
     * @return Project
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
     * Set objectID
     *
     * @param string $objectID
     * @return Project
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
     * Set objectUUID
     *
     * @param string $objectUUID
     * @return Project
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
}
