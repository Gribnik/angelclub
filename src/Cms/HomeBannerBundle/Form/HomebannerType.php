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
        $builder->add('file', 'file', array('required' => false));
        $builder->add('description', 'textarea', array('required' => false));
    }
}