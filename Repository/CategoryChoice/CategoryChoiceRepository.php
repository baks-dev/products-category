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

namespace BaksDev\Products\Category\Repository\CategoryChoice;

use BaksDev\Core\Doctrine\ORMQueryBuilder;
use BaksDev\Products\Category\Entity\Event\CategoryProductEvent;
use BaksDev\Products\Category\Entity\Info\CategoryProductInfo;
use BaksDev\Products\Category\Entity\CategoryProduct;
use BaksDev\Products\Category\Entity\Trans\CategoryProductTrans;
use BaksDev\Products\Category\Type\Id\CategoryProductUid;

final class CategoryChoiceRepository implements CategoryChoiceInterface
{

    private ORMQueryBuilder $ORMQueryBuilder;

    public function __construct(ORMQueryBuilder $ORMQueryBuilder,)
    {
        $this->ORMQueryBuilder = $ORMQueryBuilder;
    }

    /** Метод возвращает коллекцию категорий продукции */
    public function getCategoryCollection(?CategoryProductUid $category = null): ?array
    {
        $orm = $this
            ->ORMQueryBuilder->createQueryBuilder(self::class)
            ->bindLocal();

        $select = sprintf('new %s(category.id, trans.name)', CategoryProductUid::class);

        $orm->select($select);

        $orm->from(CategoryProduct::class, 'category', 'category.id');

        /* Выбираем только активные */
        $orm->join(
            CategoryProductInfo::class,
            'info',
            'WITH',
            'info.event = category.event AND info.active = true',
        );

        $orm->join(
            CategoryProductEvent::class,
            'event',
            'WITH',
            'event.id = category.event AND event.category = category.id',
        );
        $orm->leftJoin(
            CategoryProductTrans::class,
            'trans',
            'WITH',
            'trans.event = event.id AND trans.local = :local',
        );

        /* Кешируем результат ORM */
        return $orm->enableCache('products-category', 86400)->getResult();
    }


    /**
     * Метод возвращает идентификатор категории с названием в аттрибуте
     */
    public function getProductCategory(CategoryProduct|CategoryProductUid|string $category): ?CategoryProductUid
    {
        if($category instanceof CategoryProduct)
        {
            $category = $category->getId();
        }

        if(is_string($category))
        {
            $category = new CategoryProductUid($category);
        }

        $orm = $this->ORMQueryBuilder
            ->createQueryBuilder(self::class)
            ->bindLocal();

        $select = sprintf('new %s(category.id, trans.name)', CategoryProductUid::class);

        $orm->select($select);

        $orm
            ->from(CategoryProduct::class, 'category', 'category.id')
            ->where('category.id = :category')
            ->setParameter('category', $category, CategoryProductUid::TYPE);

        /* Выбираем только активные */
        $orm->join(
            CategoryProductInfo::class,
            'info',
            'WITH',
            'info.event = category.event AND info.active = true',
        );

        $orm->join(
            CategoryProductEvent::class,
            'event',
            'WITH',
            'event.id = category.event AND event.category = category.id',
        );
        $orm->leftJoin(
            CategoryProductTrans::class,
            'trans',
            'WITH',
            'trans.event = event.id AND trans.local = :local',
        );


        /* Кешируем результат ORM */
        return $orm->enableCache('products-category', 86400)->getOneOrNullResult();
    }
}