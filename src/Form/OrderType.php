<?php

namespace App\Form;

use App\Entity\Order;
use App\Entity\Customers;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ->add('orderDate', DateType::class, [
            //     'widget' => 'single_text', // Hiển thị dưới dạng input text
            //     'attr' => ['class' => 'form-control'], // Style input
            //     'label' => 'Order Date',
            // ])
            ->add('customer', EntityType::class, [
                'class' => Customers::class, // Entity Customer
                'choice_label' => 'fullName', // Thuộc tính hiển thị trong dropdown (ví dụ: 'fullName')
                'placeholder' => 'Select a customer', // Tùy chọn hiển thị mặc định
                'label' => 'Customer', // Nhãn hiển thị
                'attr' => ['class' => 'form-select'], // Style dropdown
            ])
            ->add('totalAmount', MoneyType::class, [
                'currency' => 'USD', // Đơn vị tiền tệ
                'attr' => ['class' => 'form-control'], // Style input
                'label' => 'Total Amount',
            ])
            ->add('note', TextareaType::class, [
                'required' => false,
                'attr' => ['class' => 'form-control', 'rows' => 4],
                'label' => 'Note',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class, 
            'csrf_protection' => false,
        ]);
    }
}