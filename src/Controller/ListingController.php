<?php

namespace App\Controller;

use App\Form\Admin\ListingType;
use Doctrine\Inflector\Inflector;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\Query\Expr;

class ListingController extends AbstractController
{
    /**
     * Nombre d'item par page par défaut, peut être overwrité dans l'entité listé (en définissant cette même constante)
     */
    const DEFAULT_ITEMS_NUMBER = 10;
    const CUSTOM_ORDER_IDENTIFIER   = 'custom_order:';
    const CUSTOM_FILTER_IDENTIFIER  = 'custom_filter:';

    /*
     * Champ qui ne sera pas rendu au même endroit que les autres. Il sera traité, par défaut, comme un CUSTOM_FILTER
     */
    const SPECIAL_FILTER_IDENTIFIER = 'special_filter:';

    /**
     * Vérifie si une table est déjà jointe, en fonction du nom de l'association
     *
     * @param QueryBuilder $query
     * @param string $fullAssociation
     * @param string $alias
     * @param string $context
     * @return bool
     */
    protected function joinExistsInQuery(QueryBuilder $query, string $fullAssociation, string $alias, string $context = 'main')
    {
        $parts = $query->getDQLParts();
        $fragments = explode('.', $fullAssociation);
        if(count($fragments) !== 2) {
            return false;
        }
        $parent = $fragments[0];
        $association = $fragments[1];

        if(isset($parts['join']) && isset($parts['join'][$context])) {
            foreach($parts['join'][$context] as $join) {
                /** @var Expr\Join $join */
                if($join->getJoin() === $fullAssociation && $join->getAlias() === $alias) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @param QueryBuilder $query
     * @param Form $form
     * @param string $class
     * @param array $options
     * @return array
     */
    protected function processQueryForEntity(QueryBuilder $query, Form $form, string $class, $options = [])
    {
        $items = new Paginator($query, true);
        $params = [];

        try {

            // Si on est hors limite, retour à la page 1
            if($items->count() <= $items->getQuery()->getFirstResult()) {
                $items->getQuery()->setFirstResult(0);
                $params['resetOffset'] = true;
            }

            $params = $params + [
                'items' => $items,
                'listingForm' => $form->createView(),
            ];

            // Possibilité de tout afficher (paramètre no_limit)
            if(!isset($options['no_limit']) || true !== $options['no_limit']) {
                $params = $params + [
                    'itemsPerPage' => property_exists($class, 'DEFAULT_ITEMS_NUMBER') ? $class::DEFAULT_ITEMS_NUMBER : self::DEFAULT_ITEMS_NUMBER,
                ];
            }

        } catch (\Exception $exception) {

            if($this->getParameter('kernel.environment') == 'dev') {
                dd($exception->getMessage(), $query->getDQL(), $query->getParameters(), $query->getQuery()->getSQL());
            }
        }

        return $params;
    }

    /**
     * @param Request $request
     * @param string $class
     * @param string $filtersForm
     * @param array $filtersFormParams
     * @return array
     * @throws \Exception
     */
    protected final function getQueryBuilderAndFormFromRequest(Request $request,ManagerRegistry $doctrine, string $class, string $filtersForm = '', Array $filtersFormParams = [],)
    {
        $objectRepository = $doctrine->getRepository($class);

        // Récupération du formulaire des filtres, si nécessaire
        $options = [];
        if($filtersForm) {
            $options['filtersForm'] = $filtersForm;
            // Si on a des options à faire passer à ce sous-formulaire
            if($filtersFormParams) {
                $options['filtersFormOptions'] = $filtersFormParams;
            }
        }

        $form = $this->createForm(ListingType::class, null, $options);
        $form->handleRequest($request);

        /** @var QueryBuilder $query */
        $query      = $objectRepository->createQueryBuilder('main');
        $manager    = $doctrine->getManager();
        $metas      = $manager->getClassMetadata($class);

        // Allowed columns
        $columns    = $metas->getFieldNames();
        $assocs     = $metas->getAssociationNames();

        // Search :
        if ($form->has('search')) {
            $search = $form->get('search')->getData();
            if(!empty($search))
            {
                $like = $query->expr()->literal('%'.$search.'%');

                foreach($columns as $column)
                {
                    $query->orWhere($query->expr()->like('main.'.$column, $like));
                }

                foreach($assocs as $assoc)
                {
                    $parts = explode('.', $assoc);

                    // Si on recherche sur une table enfant ou simplement sur un attribut
                    if(count($parts) === 1 || count($parts) === 2) {
                        $associationParent = $parts[0];
                    } else {
                        throw new \Exception('Not implemented yet.');
                    }

                    $query->leftJoin('main.' . $associationParent, 'main_' . $associationParent);

                    // On recherche dans les attributs de l'entité join :
                    $assocClass = $metas->getAssociationTargetClass($assoc);
                    $assocMetas = $manager->getClassMetadata($assocClass);

                    foreach($assocMetas->getFieldNames() as $field)
                    {
                        $query->orWhere($query->expr()->like('main_' . $associationParent . '.' . $field, $like));
                    }
                }
            }
        }

        // Filtering
        if($form->has('filters')) {
            $filterIdx = 0;
            // Si on a des filtres
            foreach($form->get('filters')->all() as $field) {
                // Si on est pas sur un filtre custom ou spécial
                if(
                    strpos($field->getName(), self::CUSTOM_FILTER_IDENTIFIER) === false &&
                    strpos($field->getName(), self::SPECIAL_FILTER_IDENTIFIER) === false
                ) {
                    // Récupération du type de champ
                    $fieldType = get_class($field->getConfig()->getType()->getInnerType());

                    // Selon le type de champ
                    switch ($fieldType) {
                        case EntityType::class : {
                            $selectedEntity = $field->getData();
                            // Si on a sélectionné une entité
                            if (isset($selectedEntity) and is_object($selectedEntity)) {
                                $attribute = 'main.' . $field->getName();
                                $query->andWhere($query->expr()->eq($attribute, $selectedEntity->getId()));
                            }
                            break;
                        }
                        case ChoiceType::class : {
                            // Si on a fait un choix
                            $value = $field->getData();
                            if (isset($value) && $value !== '') {
                                $attribute = 'main.' . $field->getName();
                                $query->andWhere($query->expr()->eq($attribute, ':filter_' . $filterIdx));
                                $query->setParameter(':filter_' . $filterIdx, $value);
                            }
                            break;
                        }
                        case TextType::class : {
                            // Si on recherche une chaine de caractères
                            $value = $field->getData();
                            if (isset($value) && $value !== '') {
                                $attribute = 'main.' . $field->getName();
                                $query->andWhere($query->expr()->like($attribute, ':filter_' . $filterIdx));
                                $query->setParameter(':filter_' . $filterIdx, '%' . $value . '%');
                            }
                            break;
                        }
                        default : {
                            throw new \Exception('Unhandled field type : ' . $fieldType);
                        }
                    }

                    $filterIdx++;
                }
            }
        }

        // Ordering :
        $order      = $form->get('order')->getData();
        $direction  = $form->get('direction')->getData();
        if($direction != 'DESC')
            $direction = 'ASC';

        // Si on est pas sur un order by custom
        if(strpos($order, self::CUSTOM_ORDER_IDENTIFIER) === false) {
            if (!empty($order) && in_array($order, $columns + $assocs)) {
                $query->orderBy('main.' . $order, $direction);
            }
        }

        // Pagination
        $limit = $form->get('limit')->getData();
        $offset = $form->get('offset')->getData();
        if(!$limit) {
            $limit = defined($class . '::DEFAULT_ITEMS_NUMBER') ? $class::DEFAULT_ITEMS_NUMBER : self::DEFAULT_ITEMS_NUMBER;
        }

        if($limit) {
            $query
                ->setFirstResult($offset)
                ->setMaxResults($limit);
        }

        return [
            'query' => $query,
            'form' => $form,
        ];
    }
}
