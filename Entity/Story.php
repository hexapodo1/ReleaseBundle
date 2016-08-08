<?php

namespace Kishron\ReleaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Story
 *
 * @ORM\Table(name="story")
 * @ORM\Entity(repositoryClass="Kishron\ReleaseBundle\Repository\StoryRepository")
 */
class Story
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;
    
    /**
     * @var string
     *
     * @ORM\Column(name="rally_url", type="string", length=512)
     */
    private $rallyUrl;

    /**
     * @ORM\ManyToOne(targetEntity="ReleaseObj", inversedBy="stories")
     * @ORM\JoinColumn(name="release_id", referencedColumnName="id")
     */
    private $release;
    
    /**
     * @ORM\OneToMany(targetEntity="Revision", mappedBy="story")
     */
    private $revisions;

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
     * @return Story
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
     * Set name
     *
     * @param string $name
     * @return Story
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
     * Set rallyUrl
     *
     * @param string $rallyUrl
     * @return Story
     */
    public function setRallyUrl($rallyUrl)
    {
        $this->rallyUrl = $rallyUrl;

        return $this;
    }

    /**
     * Get rallyUrl
     *
     * @return string 
     */
    public function getRallyUrl()
    {
        return $this->rallyUrl;
    }

    /**
     * Set release
     *
     * @param \Kishron\ReleaseBundle\Entity\ReleaseObj $release
     * @return Story
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

    /**
     * Add revisions
     *
     * @param \Kishron\ReleaseBundle\Entity\Revision $revisions
     * @return Story
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
}
