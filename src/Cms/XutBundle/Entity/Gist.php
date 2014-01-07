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
     * @ORM\Column(name="name", type="string", length=80)
     */
    private $name;

    /**
     * @ORM\Column(name="type", type="string", length=30)
     */
    private $type;

    /**
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @ORM\ManyToMany(targetEntity="Category",inversedBy="gists")
     */
    private $categories;

    /**
     * @ORM\ManyToMany(targetEntity="Tag",inversedBy="gists")
     */
    private $tags;

    /**
     * @ORM\Column(name="featured_image", type="string", length=200)
     */
    private $featured_image;

    /**
     * @ORM\Column(name="date_created", type="datetime")
     */
    private $date_created;

    /**
     * @ORM\Column(name="date_updated", type="datetime")
     */
    private $date_updated;

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

    /**
     * Set date_created
     *
     * @param \DateTime $dateCreated
     * @return Gist
     */
    public function setDateCreated($dateCreated)
    {
        $this->date_created = $dateCreated;
    
        return $this;
    }

    /**
     * Get date_created
     *
     * @return \DateTime 
     */
    public function getDateCreated()
    {
        return $this->date_created;
    }

    /**
     * Set date_updated
     *
     * @param \DateTime $dateUpdated
     * @return Gist
     */
    public function setDateUpdated($dateUpdated)
    {
        $this->date_updated = $dateUpdated;
    
        return $this;
    }

    /**
     * Get date_updated
     *
     * @return \DateTime 
     */
    public function getDateUpdated()
    {
        return $this->date_updated;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->categories = new \Doctrine\Common\Collections\ArrayCollection();
        $this->tags = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Set featured_image
     *
     * @param string $featuredImage
     * @return Gist
     */
    public function setFeaturedImage($featuredImage)
    {
        $this->featured_image = $featuredImage;
    
        return $this;
    }

    /**
     * Get featured_image
     *
     * @return string 
     */
    public function getFeaturedImage()
    {
        return $this->featured_image;
    }

    /**
     * Add categories
     *
     * @param \Cms\XutBundle\Entity\Category $categories
     * @return Gist
     */
    public function addCategorie(\Cms\XutBundle\Entity\Category $categories)
    {
        $this->categories[] = $categories;
    
        return $this;
    }

    /**
     * Remove categories
     *
     * @param \Cms\XutBundle\Entity\Category $categories
     */
    public function removeCategorie(\Cms\XutBundle\Entity\Category $categories)
    {
        $this->categories->removeElement($categories);
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Add tags
     *
     * @param \Cms\XutBundle\Entity\Tag $tags
     * @return Gist
     */
    public function addTag(\Cms\XutBundle\Entity\Tag $tags)
    {
        $this->tags[] = $tags;
    
        return $this;
    }

    /**
     * Remove tags
     *
     * @param \Cms\XutBundle\Entity\Tag $tags
     */
    public function removeTag(\Cms\XutBundle\Entity\Tag $tags)
    {
        $this->tags->removeElement($tags);
    }

    /**
     * Remove all tags
     */
    public function removeAllTags()
    {
        $this->tags = array();
    }

    /**
     * Do nothing with the tags string
     *
     * @param string $tags
     */
    public function setTags($tags)
    {

    }

    /**
     * Get tags
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTags()
    {
        return $this->tags;
    }
}