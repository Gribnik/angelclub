<?php
namespace Cms\XutBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Table(name="cms_tag")
 * @ORM\Entity()
 */
class Tag
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="name", type="string", length=80)
     */
    private $name;

    /**
     * @ORM\Column(name="type", type="string", length=30)
     */
    private $type;

    /**
     * @ORM\ManyToMany(targetEntity="Gist", mappedBy="tags")
     */
    private $gists;

    public function __construct()
    {
        $this->$gists = new ArrayCollection();
    }

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
     * @return Tag
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
     * Set type
     *
     * @param string $type
     * @return Tag
     */
    public function setType($type)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Add gists
     *
     * @param \Cms\XutBundle\Entity\Gist $gists
     * @return Tag
     */
    public function addGist(\Cms\XutBundle\Entity\Gist $gists)
    {
        $this->gists[] = $gists;
    
        return $this;
    }

    /**
     * Remove gists
     *
     * @param \Cms\XutBundle\Entity\Gist $gists
     */
    public function removeGist(\Cms\XutBundle\Entity\Gist $gists)
    {
        $this->gists->removeElement($gists);
    }

    /**
     * Get gists
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGists()
    {
        return $this->gists;
    }
}