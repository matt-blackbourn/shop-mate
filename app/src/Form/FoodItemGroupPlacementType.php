<?php

namespace App\Form;

use App\Entity\FoodItem;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FoodItemGroupPlacementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('groupWithItem', EntityType::class, [
                'class' => FoodItem::class,
                'choices' => $options['food_items'],
                'choice_label' => 'name',
                'placeholder' => false,
                'required' => true,
                'mapped' => false,
                'placeholder' => 'Search for item...',
                'label' => false,
            ])
            ->add('supermarketId', HiddenType::class)
            ->add('foodItemId', HiddenType::class)
            ->add('listId', HiddenType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('food_items');
    }
}
