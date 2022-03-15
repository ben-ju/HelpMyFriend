<?php

namespace App\Form;

use App\Entity\Locataire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocataireForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('prenom')
            ->add('telephone')
            ->add('roles')
            ->add('email')
            ->add('numero_etat')
            ->add('id_utilisateur_fk')
            ->add('id_groupe_fk')
            ->add('submit', SubmitType::class);;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Locataire::class,
        ]);
    }
}
