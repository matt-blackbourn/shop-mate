<?php

namespace App\Form;

use App\Entity\FoodCategory;
use App\Entity\FoodItem;
use Dom\Entity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FoodItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('category', EntityType::class, [
                'class' => FoodCategory::class,
                'choice_label' => 'name',
                'placeholder' => 'Add to Group',
                'required' => false,
                'label' => 'Put this item in one of the groups below to help organise your list:',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FoodItem::class,
        ]);
    }
}
