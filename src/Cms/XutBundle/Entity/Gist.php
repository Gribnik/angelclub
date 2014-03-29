<?php
namespace Cms\XutBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
     * @ORM\Column(name="name", type="string", length=80, nullable=true)
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
     * @ORM\Column(name="featured_image", type="string", length=200, nullable=true)
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

    private $file;

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
        foreach ($this->tags as $_tag) {
            $this->removeTag($_tag);
        }
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

    public function getTagNames()
    {
        $tagnames = array();
        /** Cms\XutBundle\Entity\Tag $_tag  */
        foreach ($this->tags as $_tag) {
            array_push($tagnames, $_tag->getName());
        }

        return implode(',', $tagnames);
    }

    public function getTagsfield()
    {
       return $this->getTagNames();
    }

    public function setTagsfield()
    {

    }

    /* TODO: Remove duplicated logic */

    public function getAbsolutePath()
    {
        return null === $this->image
            ? null
            : $this->getUploadRootDir().'/'.$this->featured_image;
    }

    public function getWebPath()
    {
        return null === $this->featured_image
            ? null
            : $this->getUploadDir().'/'.$this->featured_image;
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    public function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return '/uploads/gallery/';
    }

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    public function upload()
    {
        // the file property can be empty if the field is not required
        if (null === $this->getFile()) {
            return;
        }

        // use the original file name here but you should
        // sanitize it at least to avoid any security issues

        // move takes the target directory and then the
        // target filename to move to
        $this->getFile()->move(
            $this->getUploadRootDir(),
            $this->getFile()->getClientOriginalName()
        );

        $originalName = $this->getFile()->getClientOriginalName();

        // set the path property to the filename where you've saved the file
        $this->setFeaturedImage($originalName);

        // clean up the file property as you won't need it anymore
        $this->file = null;

        return $originalName;
    }
}