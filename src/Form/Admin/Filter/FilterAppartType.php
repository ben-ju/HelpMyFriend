<?php

namespace App\Form\Admin\Filter;

use App\Controller\ListingController;
use App\Entity\Appart;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;


class FilterAppartType extends AbstractType
{

    private EntityManagerInterface $entityManager;
    /**
     * @var ParameterBagInterface
     */
    private ParameterBagInterface $params;

    /**
     * @var Security
     */
    private Security $security;


    /**
     * StudentType constructor.
     * @param EntityManagerInterface $entityManager
     * @param ParameterBagInterface $params
     * * @param Security $security
     */
    public function __construct(EntityManagerInterface $entityManager, ParameterBagInterface $params, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->params = $params;
        $this->security = $security;
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