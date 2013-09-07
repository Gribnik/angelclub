<?php
namespace Cms\XutBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="cms_gist")
 * @ORM\Entity()
 */
class Gist
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="title_id", type="string", length=30)
     */
    private $titleId; /* TODO: add index */

    /**
     * @ORM\Column(name="name", type="string", length=80)
     */
    private $name;

    /**
     * @ORM\Column(name="type", type="string", length=30)
     */
    private $type; /* TODO: add index */

    /**
     * @ORM\Column(name="content", type="text")
     */
    private $content;

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
     * Set titleId
     *
     * @param string $titleId
     * @return Gist
     */
    public function setTitleId($titleId)
    {
        $this->titleId = $titleId;
    
        return $this;
    }

    /**
     * Get titleId
     *
     * @return string 
     */
    public function getTitleId()
    {
        return $this->titleId;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Gist
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
     * @return Gist
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
     * Set content
     *
     * @param string $content
     * @return Gist
     */
    public function setContent($content)
    {
        $this->content = $content;
    
        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }
}