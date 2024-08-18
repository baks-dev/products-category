<?php
/*
 *  Copyright 2023.  Baks.dev <admin@baks.dev>
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

namespace BaksDev\Products\Category\Repository\CategoryPropertyById;

use BaksDev\Core\Doctrine\ORMQueryBuilder;
use BaksDev\Products\Category\Entity\CategoryProduct;
use BaksDev\Products\Category\Entity\Event\CategoryProductEvent;
use BaksDev\Products\Category\Entity\Section\CategoryProductSection;
use BaksDev\Products\Category\Entity\Section\Field\CategoryProductSectionField;
use BaksDev\Products\Category\Entity\Section\Field\Trans\CategoryProductSectionFieldTrans;
use BaksDev\Products\Category\Entity\Section\Trans\CategoryProductSectionTrans;
use BaksDev\Products\Category\Type\Id\CategoryProductUid;

final class CategoryPropertyByIdRepository implements CategoryPropertyByIdInterface
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
            $category = new CategoryProductUid($category);
        }

        $this->category = $category;
        return $this;
    }


    public function findAllProperty(): ?array
    {

        $orm = $this->ORMQueryBuilder
            ->createQueryBuilder(self::class)
            ->bindLocal();

        $select = sprintf(
            '
          NEW %s(
              section.id,
              section_trans.name,
              field.const,
              field_trans.name,
              field.type,
              field.required,
              field_trans.description
          )',
            CategoryPropertyDTO::class
        );

        $orm->select($select);

        $orm
            ->from(CategoryProduct::class, 'category');

        if($this->category)
        {
            $orm
                ->where('category.id = :category')
                ->setParameter(
                    'category',
                    $this->category,
                    CategoryProductUid::TYPE
                );
        }

        $orm->join(
            CategoryProductEvent::class,
            'category_event',
            'WITH',
            'category_event.id = category.event'
        );

        /* Секции свойств */
        $orm->join(
            CategoryProductSection::class,
            'section',
            'WITH',
            '  section.event = category_event.id'
        );

        /* Перевод секции */
        $orm->join(
            CategoryProductSectionTrans::class,
            'section_trans',
            'WITH',
            'section_trans.section = section.id AND section_trans.local = :local'
        );


        /* Перевод полей */
        //$orm->addSelect('field.id');
        $orm->join(
            CategoryProductSectionField::class,
            'field',
            'WITH',
            'field.section = section.id AND field.const IS NOT NULL'
        );


        $orm->join(
            CategoryProductSectionFieldTrans::class,
            'field_trans',
            'WITH',
            'field_trans.field = field.id AND field_trans.local = :local'
        );


        $orm->orderBy('section.sort', 'ASC');
        $orm->addOrderBy('field.sort', 'ASC');


        /* Преобразуем ключи */
        $dto = null;

        foreach($orm->getQuery()->getResult() as $item)
        {
            $dto[(string) $item->fieldUid] = $item;
        }

        return $dto;
    }

}
