<?php

namespace App\Form\Admin\Filter;

use App\Entity\Appart;
use App\Controller\ListingController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class FilterAppartType extends AbstractType
{

    private EntityManagerInterface $entityManager;
    /**
     * @var ParameterBagInterface
     */
    private ParameterBagInterface $params;

    /**
     * StudentType constructor.
     * @param EntityManagerInterface $entityManager
     * @param ParameterBagInterface $params
     */
    public function __construct(EntityManagerInterface $entityManager, ParameterBagInterface $params)
    {
        $this->entityManager = $entityManager;
        $this->params = $params;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(ListingController::CUSTOM_FILTER_IDENTIFIER.'search', TextType::class, [
                'label' => 'Rechercher un Logement  : ',
                'required' => false,
                'attr' => ['placeholder' => 'tapez une adresse'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'admin',
        ]);
    }
}