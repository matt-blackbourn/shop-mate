<?php

namespace App\Form;

use App\Enum\SupermarketType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FloorPlanType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('supermarketType', ChoiceType::class, [
                'choices' => array_combine(
                    // Labels: human-readable from enum names
                    array_map(
                        fn(SupermarketType $case) => ucwords(str_replace('_', ' ', strtolower($case->name))),
                        SupermarketType::cases()
                    ),
                    // Values: enum backing values
                    array_map(fn(SupermarketType $case) => $case->value, SupermarketType::cases())
                ),
                'label' => 'Supermarket Type',
                'required' => true,
            ])
            ->add('suburb', TextType::class, [
                'label' => 'Suburb',
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
             // no data_class because we’ll merge into payload manually
        ]);
    }
}
