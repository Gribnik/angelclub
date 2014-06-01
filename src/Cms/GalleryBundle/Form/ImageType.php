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
        $builder->add('file', 'file', array('required' => false, 'label' => ''));
        $builder->add('content', 'textarea', array('required' => false));
        $builder->add('tagsfield', 'text', array('required' => false));
        $builder->add('categories', 'entity', array(
            'multiple' => true,
            'expanded' => false,
            'property' => 'name',
            'class'    => 'CmsXutBundle:Category'
        ));
    }
}