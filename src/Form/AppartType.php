<?php

namespace App\Form;

use App\Entity\Appart;
use App\Entity\Hebergeur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AppartType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('ville')
            ->add('adresse')
            ->add('code_postal')
            ->add('places_disponibles')
            ->add('id_utilisateur_fk', EntityType::class, [
                'class' => Hebergeur::class,
                'choice_label' => 'id_utilisateur_fk',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Ajouter mon hébergement'
            ])
            ->getForm();

        // ->add('id_utilisateur_fk', ChoiceType::class, [
        //     'choices'  => [
        //         'Actif' => 1,
        //         'Inactif' => 0,
        //     ],
        // ])
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Appart::class,
            'mapped' => false,
            // 'id_utilisateur_fk' => false,
        ]);
        // $resolver->setAllowedTypes('id_utilisateur_fk', 'array');
    }
}
