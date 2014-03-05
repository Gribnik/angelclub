<?php

namespace Cms\GalleryBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ImageType extends AbstractType
{
    public function getName()
    {
        return 'gallery_image';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('file', 'file');
        $builder->add('content', 'textarea', array('required' => false));
        $builder->add('tagsfield', 'text', array('required' => false));
    }
}