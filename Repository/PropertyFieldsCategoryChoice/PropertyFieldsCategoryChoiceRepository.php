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
use BaksDev\Products\Category\Entity\CategoryProduct;
use BaksDev\Products\Category\Entity\Event\CategoryProductEvent;
use BaksDev\Products\Category\Entity\Section\CategoryProductSection;
use BaksDev\Products\Category\Entity\Section\Field\CategoryProductSectionField;
use BaksDev\Products\Category\Entity\Section\Field\Trans\CategoryProductSectionFieldTrans;
use BaksDev\Products\Category\Entity\Section\Trans\CategoryProductSectionTrans;
use BaksDev\Products\Category\Type\Id\CategoryProductUid;
use BaksDev\Products\Category\Type\Section\Field\Id\CategoryProductSectionFieldUid;

final class PropertyFieldsCategoryChoiceRepository implements PropertyFieldsCategoryChoiceInterface
{
    private string|CategoryProduct|CategoryProductUid $category;

    public function __construct(private readonly ORMQueryBuilder $ORMQueryBuilder) {}

    public function category(CategoryProduct|CategoryProductUid|string $category): self
    {
        if($category instanceof CategoryProduct)
        {
            $category = $category->getId();
        }

        if(is_string($category))
        {
            $category = new CategoryProductUid();
        }

        $this->category = $category;
        return $this;
    }

    /**
     * Метод возвращает список всех свойств
     */
    public function getPropertyFieldsCollection(): ?array
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

        $qb->from(CategoryProduct::class, 'category');

        if($this->category)
        {
            $qb
                ->where('category.id = :category')
                ->setParameter('category', $this->category, CategoryProductUid::TYPE);
        }

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
