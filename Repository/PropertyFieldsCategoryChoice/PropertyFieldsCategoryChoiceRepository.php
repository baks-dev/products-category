<?php
/*
 *  Copyright 2025.  Baks.dev <admin@baks.dev>
 *  
 *  Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is furnished
 *  to do so, subject to the following conditions:
 *  
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 *  
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NON INFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *  THE SOFTWARE.
 */

namespace BaksDev\Products\Category\Repository\PropertyFieldsCategoryChoice;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Core\Doctrine\ORMQueryBuilder;
use BaksDev\Products\Category\Entity\CategoryProduct;
use BaksDev\Products\Category\Entity\Event\CategoryProductEvent;
use BaksDev\Products\Category\Entity\Section\CategoryProductSection;
use BaksDev\Products\Category\Entity\Section\Field\CategoryProductSectionField;
use BaksDev\Products\Category\Entity\Section\Field\Trans\CategoryProductSectionFieldTrans;
use BaksDev\Products\Category\Entity\Section\Trans\CategoryProductSectionTrans;
use BaksDev\Products\Category\Type\Id\CategoryProductUid;
use BaksDev\Products\Category\Type\Section\Field\Id\CategoryProductSectionFieldUid;
use Generator;

final class PropertyFieldsCategoryChoiceRepository implements PropertyFieldsCategoryChoiceInterface
{
    private ?CategoryProductUid $category = null;

    public function __construct(
        private readonly ORMQueryBuilder $ORMQueryBuilder,
        private readonly DBALQueryBuilder $DBALQueryBuilder
    ) {}

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
        $qb = $this->ORMQueryBuilder
            ->createQueryBuilder(self::class)
            ->bindLocal();

        $select = sprintf(
            'NEW %s(
              field.const,
              field.id,
              field_trans.name,
              field.type
          )',
            CategoryProductSectionFieldUid::class,
        );

        $qb->select($select);

        $qb->from(CategoryProduct::class, 'category');

        if($this->category)
        {
            $qb
                ->where('category.id = :category')
                ->setParameter(
                    key: 'category',
                    value: $this->category,
                    type: CategoryProductUid::TYPE
                );
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


    /**
     * Метод возвращает список всех свойств
     */
    public function newPropertyFieldsCollection(): Generator|false
    {
        $dbal = $this->DBALQueryBuilder
            ->createQueryBuilder(self::class)
            ->bindLocal();

        $dbal->from(CategoryProduct::class, 'category');

        if($this->category)
        {
            $dbal
                ->where('category.id = :category')
                ->setParameter('category', $this->category, CategoryProductUid::TYPE);
        }

        $dbal->join(
            'category',
            CategoryProductEvent::class,
            'category_event',
            'category_event.id = category.event',
        );

        /* Секции свойств */
        $dbal->join(
            'category_event',
            CategoryProductSection::class,
            'section',
            'section.event = category_event.id',
        );

        /* Перевод секции */
        $dbal->join(
            'section',
            CategoryProductSectionTrans::class,
            'section_trans',
            'section_trans.section = section.id AND section_trans.local = :local',
        );

        /* Перевод полей */
        $dbal->join(
            'section',
            CategoryProductSectionField::class,
            'field',
            'field.section = section.id',
        );

        $dbal->join(
            'field',
            CategoryProductSectionFieldTrans::class,
            'field_trans',
            'field_trans.field = field.id AND field_trans.local = :local',
        );

        $dbal->orderBy('section.sort', 'ASC');
        $dbal->addOrderBy('field.sort', 'ASC');


        /** Параметры конструктора объекта гидрации */
        $dbal->select('field.const AS value');
        $dbal->addSelect('field.id AS const');
        $dbal->addSelect('field_trans.name AS attr');
        $dbal->addSelect('field.type AS property');

        return $dbal->fetchAllHydrate(CategoryProductSectionFieldUid::class);
    }
}
