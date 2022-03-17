<?php


namespace App\Controller;


use App\Entity\Appart;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

class AppartListingController extends ListingController
{
    const ENTITY = Appart::class;

    const ALLOWED_SORTS = [
        ListingController::CUSTOM_ORDER_IDENTIFIER . '#',
        'id_redmine',
        'createdDate'
    ];

    const QUERY_ALL = 'query_all';

    private $defaultOrderMode;

    private $defaultOrderDirection;


    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getItemsFromRequest(Request $request, ManagerRegistry $doctrine ,string $filtersForm = '', array $filtersFormParams = [], string $decorate = '', array $decorateParams = [])
    {
        $data = $this->getQueryBuilderAndFormFromRequest($request,$doctrine, self::ENTITY, $filtersForm, $filtersFormParams);

        /** @var QueryBuilder $query */
        $query = $data['query'];
        /** @var Form $form */
        $form = $data['form'];

        if (isset($decorate)) {
            switch ($decorate) {
                case self::QUERY_ALL:
                default:
                {
                    break;
                }
            }
        }


        return $this->processQuery($query, $form);
    }

    /**
     * Définir l'ordre de tri par défaut
     *
     * @param string $orderMode
     * @param string $direction
     */
    public function setDefaultOrder(string $orderMode, string $direction = 'ASC')
    {
        if (in_array($orderMode, self::ALLOWED_SORTS) && in_array($direction, [
                'ASC',
                'DESC',
            ])) {
            $this->defaultOrderMode = $orderMode;
            $this->defaultOrderDirection = $direction;
        }
    }

    public function processQuery(QueryBuilder $query, Form $form, $options = [])
    {
        // Gestion de l'ordering et de la direction
        $order = $form->get('order')->getData();
        $direction = $form->get('direction')->getData();
        if (!in_array($direction, ['ASC', 'DESC'])) {
            unset($direction);
        }

        if (empty($order)) {
            if (isset($this->defaultOrderMode)) {
                $order = $this->defaultOrderMode;
            } else {
                $order = 'id';
            }
        }
        if (empty($direction)) {
            if (isset($this->defaultOrderDirection)) {
                $direction = $this->defaultOrderDirection;
            } else {
                $direction = 'DESC';
            }
        }

        if (strpos($order, parent::CUSTOM_ORDER_IDENTIFIER) === 0) {
            $customOrder = substr($order, strlen(parent::CUSTOM_ORDER_IDENTIFIER));

            // Si $customOrder finit par un _2, un _3, etc. on supprime le suffixe et on traite l'action originale
            $matches = [];
            preg_match('/_(\d+)$/', $customOrder, $matches);
            if (count($matches) === 2) {
                $customOrder = rtrim($customOrder, $matches[0]);
            }

            switch ($customOrder) {
                case '#':
                {
                    $query->orderBy('main.id', $direction);
                    break;
                }
                default :
                {
                    break;
                }
            }
        }

        // Filtering
        if ($form->has('filters')) {

            // Si on a des filtres
            foreach ($form->get('filters')->all() as $field) {

                $customFilter = $field->getName();

                // Est-ce que c'est un filtre custom ou spécial ?
                if (strpos($field->getName(), parent::CUSTOM_FILTER_IDENTIFIER) === 0) {
                    $customFilter = substr($field->getName(), strlen(parent::CUSTOM_FILTER_IDENTIFIER));
                } elseif (strpos($field->getName(), parent::SPECIAL_FILTER_IDENTIFIER) === 0) {
                    $customFilter = substr($field->getName(), strlen(parent::SPECIAL_FILTER_IDENTIFIER));
                }

                switch ($customFilter) {

                    case 'search' :
                    {
                        $value = $field->getData();
                        if (isset($value) && $value !== '') {
                            $query
                                ->andWhere('main.ville LIKE :s')
                                ->setParameter(':s', '%' . $value . '%');

                        }
                        break;
                    }
                }
            }
        }

        return parent::processQueryForEntity($query, $form, self::ENTITY, $options);
    }
}