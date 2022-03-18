<?php

namespace App\Form\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ListingType
 * Utilisé pour le tri sur les listings utilisant `ListingController`
 *
 * @package App\Form\Admin
 */
class ListingType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @throws \Exception
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('offset', HiddenType::class)
            ->add('limit', HiddenType::class)
            ->add('order', HiddenType::class)
            ->add('direction', HiddenType::class)
            ->setMethod('GET')
        ;

        // Filtres
        if(isset($options['filtersForm'])) {
            if(isset($options['filtersFormOptions'])) {
                // Si on a des options à passer à ce sous-formulaire (comme pour SearchStudentsWaitingForOfType)
                $builder->add('filters', $options['filtersForm'], $options['filtersFormOptions']);
            } else {
                // Pas d'options
                $builder->add('filters', $options['filtersForm']);
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'admin',
            'filtersForm' => null,
            'filtersFormOptions' => null,
            'user' => null
        ]);
    }
}
