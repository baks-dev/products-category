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

    public function __construct(ORMQueryBuilder $ORMQueryBuilder)
    {
        $this->ORMQueryBuilder = $ORMQueryBuilder;
    }

    /** Метод возвращает список всех свойств
     */
    public function getPropertyFieldsCollection(CategoryProductUid $category): ?array
    {
        $qb = $this->ORMQueryBuilder->createQueryBuilder(self::class)->bindLocal();

        $select = sprintf(
            'NEW %s(
              field.id,
              field.const,
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
        $qb = $this->ORMQueryBuilder->createQueryBuilder(self::class)->bindLocal();

        $select = sprintf(
            'NEW %s(
              category_offers.id,
              category_offers_tarns.name
          )',
            CategoryProductSectionFieldUid::class,
        );

        $qb->select($select);

        $qb
            ->from(CategoryProduct::class, 'category')
            ->where('category.id = :category')
            ->setParameter('category', $category, CategoryProductUid::TYPE);


        $qb->leftJoin(
            CategoryProductOffers::class,
            'category_offers',
            'WITH',
            'category_offers.event = category.event',
        );

        $qb->leftJoin(
            CategoryProductOffersTrans::class,
            'category_offers_tarns',
            'WITH',
            'category_offers_tarns.offer = category_offers.id AND category_offers_tarns.local = :local',
        );

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function getVariationFields(CategoryProductOffersUid|string $offer): ?CategoryProductSectionFieldUid
    {
        if(is_string($offer))
        {
            $offer = new CategoryProductOffersUid($offer);
        }

        $qb = $this->ORMQueryBuilder->createQueryBuilder(self::class)->bindLocal();

        $select = sprintf(
            'NEW %s(
              category_variation.id,
              category_variation_tarns.name
          )',
            CategoryProductSectionFieldUid::class,
        );

        $qb->select($select);

        $qb
            ->from(CategoryProductVariation::class, 'category_variation')
            ->where('category_variation.offer = :offer')
            ->setParameter('offer', $offer, CategoryProductOffersUid::TYPE);


        $qb->leftJoin(
            CategoryProductVariationTrans::class,
            'category_variation_tarns',
            'WITH',
            'category_variation_tarns.variation = category_variation.id AND category_variation_tarns.local = :local',
        );

        return $qb->getQuery()->getOneOrNullResult();
    }



    public function getModificationFields(CategoryProductVariationUid|string $variation): ?CategoryProductSectionFieldUid
    {
        if(is_string($variation))
        {
            $variation = new CategoryProductVariationUid($variation);
        }

        $qb = $this->ORMQueryBuilder->createQueryBuilder(self::class)->bindLocal();

        $select = sprintf(
            'NEW %s(
              category_modification.id,
              category_modification_tarns.name
          )',
            CategoryProductSectionFieldUid::class,
        );

        $qb->select($select);

        $qb
            ->from(CategoryProductModification::class, 'category_modification')
            ->where('category_modification.variation = :variation')
            ->setParameter('variation', $variation, CategoryProductVariationUid::TYPE);


        $qb->leftJoin(
            CategoryProductModificationTrans::class,
            'category_modification_tarns',
            'WITH',
            'category_modification_tarns.modification = category_modification.id AND category_modification_tarns.local = :local',
        );

        return $qb->getQuery()->getOneOrNullResult();
    }
}