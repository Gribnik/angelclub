<?php

namespace Cms\XutBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class CategoryType extends AbstractType
{
    public function getName()
    {
        return 'category';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name');
    }
}