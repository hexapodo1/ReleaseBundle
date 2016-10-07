<?php

namespace Kishron\ReleaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HipchatRoom
 *
 * @ORM\Table(name="hipchat_room")
 * @ORM\Entity(repositoryClass="Kishron\ReleaseBundle\Repository\HipchatRoomRepository")
 */
class HipchatRoom
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
     * @ORM\Column(name="code", type="string", length=255, unique=true)
     */
    private $code;
    
    /**
     * @var string
     *
     * @ORM\Column(name="auth_token", type="string", length=255, unique=true)
     */
    private $auth_token;
    
    /**
     * @ORM\OneToMany(targetEntity="DataCenter", mappedBy="hipchatRoom")
     */
    private $dataCenters;


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
     * @return HipchatRoom
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
     * Set code
     *
     * @param string $code
     * @return HipchatRoom
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
     * Constructor
     */
    public function __construct()
    {
        $this->dataCenters = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add dataCenters
     *
     * @param \Kishron\ReleaseBundle\Entity\DataCenter $dataCenters
     * @return HipchatRoom
     */
    public function addDataCenter(\Kishron\ReleaseBundle\Entity\DataCenter $dataCenters)
    {
        $this->dataCenters[] = $dataCenters;

        return $this;
    }

    /**
     * Remove dataCenters
     *
     * @param \Kishron\ReleaseBundle\Entity\DataCenter $dataCenters
     */
    public function removeDataCenter(\Kishron\ReleaseBundle\Entity\DataCenter $dataCenters)
    {
        $this->dataCenters->removeElement($dataCenters);
    }

    /**
     * Get dataCenters
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDataCenters()
    {
        return $this->dataCenters;
    }

    /**
     * Set auth_token
     *
     * @param string $authToken
     * @return HipchatRoom
     */
    public function setAuthToken($authToken)
    {
        $this->auth_token = $authToken;

        return $this;
    }

    /**
     * Get auth_token
     *
     * @return string 
     */
    public function getAuthToken()
    {
        return $this->auth_token;
    }
}
