<?php
/*
 *  Copyright 2022.  Baks.dev <admin@baks.dev>
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *  http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *   limitations under the License.
 *
 */

namespace BaksDev\Products\Category\Repository\PropertyFieldsCategoryChoice;


use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Core\Doctrine\ORMQueryBuilder;
use BaksDev\Core\Type\Locale\Locale;
use BaksDev\Products\Category\Entity\Event\CategoryProductEvent;
use BaksDev\Products\Category\Entity\CategoryProduct;
use BaksDev\Products\Category\Entity\Offers\CategoryProductOffers;
use BaksDev\Products\Category\Entity\Offers\Trans\CategoryProductOffersTrans;
use BaksDev\Products\Category\Entity\Offers\Variation\CategoryProductVariation;
use BaksDev\Products\Category\Entity\Offers\Variation\Modification\CategoryProductModification;
use BaksDev\Products\Category\Entity\Offers\Variation\Modification\Trans\CategoryProductModificationTrans;
use BaksDev\Products\Category\Entity\Offers\Variation\Trans\CategoryProductVariationTrans;
use BaksDev\Products\Category\Entity\Section\Field\CategoryProductSectionField;
use BaksDev\Products\Category\Entity\Section\Field\Trans\CategoryProductSectionFieldTrans;
use BaksDev\Products\Category\Entity\Section\CategoryProductSection;
use BaksDev\Products\Category\Entity\Section\Trans\CategoryProductSectionTrans;
use BaksDev\Products\Category\Type\Id\CategoryProductUid;
use BaksDev\Products\Category\Type\Offers\Id\CategoryProductOffersUid;
use BaksDev\Products\Category\Type\Offers\Variation\CategoryProductVariationUid;
use BaksDev\Products\Category\Type\Section\Field\Id\CategoryProductSectionFieldUid;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class PropertyFieldsCategoryChoiceRepository implements PropertyFieldsCategoryChoiceInterface
{
    private ORMQueryBuilder $ORMQueryBuilder;
    private DBALQueryBuilder $DBALQueryBuilder;

    public function __construct(
        ORMQueryBuilder $ORMQueryBuilder,
        DBALQueryBuilder $DBALQueryBuilder
    )
    {
        $this->ORMQueryBuilder = $ORMQueryBuilder;
        $this->DBALQueryBuilder = $DBALQueryBuilder;
    }

    /**
     * Метод возвращает список всех свойств
     */
    public function getPropertyFieldsCollection(CategoryProductUid $category): ?array
    {
        $qb = $this->ORMQueryBuilder->createQueryBuilder(self::class)->bindLocal();

        $select = sprintf(
            'NEW %s(
              field.const,
              field.id,
              field_trans.name
          )',
            CategoryProductSectionFieldUid::class,
        );

        $qb->select($select);

        $qb
            ->from(CategoryProduct::class, 'category')
            ->where('category.id = :category')
            ->setParameter('category', $category, CategoryProductUid::TYPE);


        $qb->join(
            CategoryProductEvent::class,
            'category_event',
            'WITH',
            'category_event.id = category.event',
        );

        /* Секции свойств */
        $qb->join(
            CategoryProductSection::class,
            'section',
            'WITH',
            '  section.event = category_event.id',
        );

        /* Перевод секции */
        $qb->join(
            CategoryProductSectionTrans::class,
            'section_trans',
            'WITH',
            'section_trans.section = section.id AND section_trans.local = :local',
        );

        /* Перевод полей */
        //$qb->addSelect('field.id');
        $qb->join(
            CategoryProductSectionField::class,
            'field',
            'WITH',
            'field.section = section.id',
        );

        $qb->join(
            CategoryProductSectionFieldTrans::class,
            'field_trans',
            'WITH',
            'field_trans.field = field.id AND field_trans.local = :local',
        );

        $qb->orderBy('section.sort', 'ASC');
        $qb->addOrderBy('field.sort', 'ASC');

        return $qb->getQuery()->getResult();
    }


    public function getOffersFields(CategoryProductUid $category): ?CategoryProductSectionFieldUid
    {
        $dbal = $this->DBALQueryBuilder
            ->createQueryBuilder(self::class)
            ->bindLocal();

        $dbal
            ->from(CategoryProduct::class, 'category')
            ->where('category.id = :category')
            ->setParameter('category', $category, CategoryProductUid::TYPE);


        $dbal->leftJoin(
            'category',
            CategoryProductOffers::class,
            'category_offers',
            'category_offers.event = category.event',
        );

        $dbal->leftJoin(
            'category_offers',
            CategoryProductOffersTrans::class,
            'category_offers_tarns',
            'category_offers_tarns.offer = category_offers.id AND category_offers_tarns.local = :local',
        );

        /** Параметры конструктора объекта гидрации */
        $dbal->select('category_offers.id AS value');
        $dbal->addSelect('category.event AS const');
        $dbal->addSelect('category_offers_tarns.name AS attr');

        return $dbal->fetchHydrate(CategoryProductSectionFieldUid::class);
    }

    public function getVariationFields(CategoryProductOffersUid|string $offer): ?CategoryProductSectionFieldUid
    {
        if(is_string($offer))
        {
            $offer = new CategoryProductOffersUid($offer);
        }

        $dbal = $this->DBALQueryBuilder
            ->createQueryBuilder(self::class)
            ->bindLocal()
        ;

        $dbal
            ->from(CategoryProductVariation::class, 'category_variation')
            ->where('category_variation.offer = :offer')
            ->setParameter('offer', $offer, CategoryProductOffersUid::TYPE);


        $dbal->leftJoin(
            'category_variation',
            CategoryProductVariationTrans::class,
            'category_variation_trans',
            'category_variation_trans.variation = category_variation.id AND category_variation_trans.local = :local',
        );

        /** Параметры конструктора объекта гидрации */
        $dbal->select('category_variation.id AS value');
        $dbal->addSelect('category_variation.offer AS const');
        $dbal->addSelect('category_variation_trans.name AS attr');

        return $dbal->fetchHydrate(CategoryProductSectionFieldUid::class); // ->getOneOrNullResult();
    }



    public function getModificationFields(CategoryProductVariationUid|string $variation): ?CategoryProductSectionFieldUid
    {
        if(is_string($variation))
        {
            $variation = new CategoryProductVariationUid($variation);
        }

        $dbal = $this->DBALQueryBuilder->createQueryBuilder(self::class)->bindLocal();

        $dbal
            ->from(CategoryProductModification::class, 'category_modification')
            ->where('category_modification.variation = :variation')
            ->setParameter('variation', $variation, CategoryProductVariationUid::TYPE);


        $dbal->leftJoin(
            'category_modification',
            CategoryProductModificationTrans::class,
            'category_modification_trans',
            'category_modification_trans.modification = category_modification.id 
            AND category_modification_trans.local = :local',
        );

        /** Параметры конструктора объекта гидрации */
        $dbal->select('category_modification.id AS value');
        $dbal->addSelect('category_modification.variation AS const');
        $dbal->addSelect('category_modification_trans.name AS attr');

        return $dbal->fetchHydrate(CategoryProductSectionFieldUid::class);
    }
}