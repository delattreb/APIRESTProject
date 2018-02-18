<?php

namespace AppBundle\Form\Type\IOT;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class CustomerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name');
        $builder->add('description');
        $builder->add('email', EmailType::class);
        $builder->add('address');
        $builder->add('city');
        $builder->add('state');
        $builder->add('postalcode');
        $builder->add('devices', CollectionType::class, [
            'entry_type' => DeviceType::class,
            'allow_add' => true,
            'error_bubbling' => false,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\IOT\Customer',
            'csrf_protection' => false
        ]);
    }
}