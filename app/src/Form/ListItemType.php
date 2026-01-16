<?php

namespace App\Form;

use App\Entity\FoodItem;
use App\Entity\ListItem;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ListItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('foodItem', EntityType::class, [
                'class' => FoodItem::class,
                'choice_label' => 'name',
                'placeholder' => 'Choose food',
            ])
            ->add('quantity', IntegerType::class, [
                'attr' => ['min' => 1],
                'required' => false,
            ])
            ->add('notes', TextType::class, [
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ListItem::class,
        ]);
    }
}