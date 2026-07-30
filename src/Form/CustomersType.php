<?php

namespace App\Form;

use App\Entity\Customers;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('PhoneNumber')
            ->add('FullName')
            // ->add('Name')
            // ->add('Age')
            ->add('DateOfBirth', null, [
                'widget' => 'single_text',
            ])
            ->add('Gender')
            ->add('Email')
            ->add('Address')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Customers::class,
            'csrf_protection' => false,
        ]);
    }
}
