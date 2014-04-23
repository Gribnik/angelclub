<?php

namespace Cms\HomeBannerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class HomebannerType extends AbstractType
{
    public function getName()
    {
        return 'home_banner';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', array('label' => 'Tag Name'));
        $builder->add('content', 'textarea', array('required' => false, 'label' => 'Excerpt'));
    }
}