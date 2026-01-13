<?php

namespace App\Form;

use App\Entity\ShoppingList;
use App\Entity\FoodItem;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ShoppingListType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('items', EntityType::class, [
                'class' => FoodItem::class,
                'choice_label' => 'name', // adjust if needed
                'multiple' => true,
                'expanded' => true, // checkboxes
                'label' => 'Items',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ShoppingList::class,
        ]);
    }
}