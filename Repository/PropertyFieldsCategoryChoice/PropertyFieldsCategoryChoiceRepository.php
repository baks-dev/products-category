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
use BaksDev\Products\Category\Entity\Section\Field\CategoryProductSectionField;
use BaksDev\Products\Category\Entity\Section\Field\Trans\CategoryProductSectionFieldTrans;
use BaksDev\Products\Category\Entity\Section\CategoryProductSection;
use BaksDev\Products\Category\Entity\Section\Trans\CategoryProductSectionTrans;
use BaksDev\Products\Category\Type\Id\CategoryProductUid;
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

}