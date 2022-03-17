<?php

namespace App\Form;

use App\Entity\Appart;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Appart1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('ville')
            ->add('adresse')
            ->add('code_postal')
            ->add('places_disponibles')
            ->add('id_utilisateur_fk')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Appart::class,
        ]);
    }
}
