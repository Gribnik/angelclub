<?php

namespace Cms\XutBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

class GistRepository extends EntityRepository
{
    /**
     * Process tags string
     *
     * @param string $tags
     * @param \Cms\XutBundle\Entity\Gist $post
     */
    public function setTagsFromString($tags, $post)
    {
        $newTags = array();
        if (!empty($tags)) {
            $tagsArray = explode(',', $tags);
            array_walk($tagsArray, array($this, '_normalizeTagNames'));

            /* Get all tags */
            $allTags = $this->findAll();

            /* Add tags to the post */
            foreach ($allTags as $_tag) {
                if (count($tagsArray) < 1) {
                    break;
                }
                if (false !== ($key = array_search($_tag->getName(), $tagsArray))) {
                    array_push($newTags, $_tag->getId());
                    $post->addTag($_tag);
                    unset($tagsArray[$key]);
                }
            }
        } else {
            /* If the tags string is empty, remove all tags */
            $post->removeAllTags();
        }
    }

    protected function _normalizeTagNames(&$tag)
    {
        $tag = trim($tag);
    }
}
