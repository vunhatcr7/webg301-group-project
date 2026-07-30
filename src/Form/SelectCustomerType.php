<?php
namespace App\Form;

use App\Entity\Customers;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SelectCustomerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Customer', EntityType::class, [
                'class' => Customers::class,
                'choice_label' => 'fullName', // Hiển thị tên đầy đủ của Customer
                'placeholder' => 'Select a customer',
                'label' => 'Customer',
                'attr' => ['class' => 'form-select'], // Style dropdown
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}