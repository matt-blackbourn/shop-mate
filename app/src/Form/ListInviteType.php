<?php

namespace App\Form;

use App\Entity\ListInvite;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ListInviteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('dateSent', null, [
                'widget' => 'single_text',
            ])
            ->add('dateAccepted', null, [
                'widget' => 'single_text',
            ])
            ->add('status')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ListInvite::class,
        ]);
    }
}
