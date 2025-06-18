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

namespace BaksDev\Products\Category\Repository\CategoryChoice;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Products\Category\Entity\CategoryProduct;
use BaksDev\Products\Category\Entity\Event\CategoryProductEvent;
use BaksDev\Products\Category\Entity\Info\CategoryProductInfo;
use BaksDev\Products\Category\Entity\Trans\CategoryProductTrans;
use BaksDev\Products\Category\Type\Id\CategoryProductUid;
use Generator;
use InvalidArgumentException;

final class CategoryChoiceRepository implements CategoryChoiceInterface
{
    /**
     * Только активные разделы
     */
    private bool $active = false;

    /**
     * Идентификатор категории
     */
    private ?CategoryProductUid $category = null;


    public function __construct(private readonly DBALQueryBuilder $DBALQueryBuilder) {}

    public function onlyActive(): self
    {
        $this->active = true;
        return $this;
    }

    /** Фильтр по идентификатору категории */
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


    public function findAll(): Generator|false
    {
        $dbal = $this->DBALQueryBuilder->createQueryBuilder(self::class)->bindLocal();

        // Категория
        $dbal
            ->select('category.id')
            //->addSelect('category.event')
            ->from(CategoryProduct::class, 'category');

        $dbal
            ->addSelect('category_event.sort')
            ->addSelect('category_event.parent')
            ->joinRecursive(
                'category',
                CategoryProductEvent::class,
                'category_event',
                'category_event.id = category.event'
            );

        $dbal
            ->addSelect('category_trans.name')
            ->leftJoin(
                'category',
                CategoryProductTrans::class,
                'category_trans',
                'category_trans.event = category.event AND category_trans.local = :local'
            );


        /** Возвращает только категории, у которых имеется продукция */
        //        $dbal
        //            ->leftOneJoin(
        //                'product_offer',
        //                ProductOfferPrice::class,
        //                'product_offer_price',
        //                'product_offer_price.offer = product_offer.id',
        //                'offer'
        //            );


        //        $dbal
        //            ->addSelect('product_category.root AS product_category')
        //            ->leftOneJoin(
        //                'category',
        //                ProductCategory::class,
        //                'product_category',
        //                'product_category.category = category.id AND product_category.root = true',
        //                'category'
        //            );

        /* Категория с определенным идентификатором */
        if($this->category)
        {
            $dbal
                ->where('category.id = :category')
                ->setParameter('category', $this->category, CategoryProductUid::TYPE);
        }


        /* Выбираем только активные */
        if($this->active)
        {
            $dbal->join(
                'category',
                CategoryProductInfo::class,
                'info',
                'info.event = category.event AND info.active = true',
            );
        }

        $result = $dbal
            ->enableCache('products-category')
            ->findAllRecursive(['parent' => 'id']);

        if(false === $result)
        {
            return false;
        }

        foreach($result as $item)
        {
            yield new CategoryProductUid($item['id'], $item['name'], $item['level']);
        }

        //        return $dbal
        //            ->enableCache('products-category', 86400)
        //            ->fetchAllHydrate(CategoryProductUid::class);
    }

    public function find(): CategoryProductUid|false
    {
        if(empty($this->category))
        {
            throw new InvalidArgumentException('Invalid Argument category');
        }

        $dbal = $this->DBALQueryBuilder
            ->createQueryBuilder(self::class)
            ->bindLocal();

        $dbal->from(CategoryProduct::class, 'category');

        /* Категория с определенным идентификатором */
        $dbal
            ->where('category.id = :category')
            ->setParameter('category', $this->category, CategoryProductUid::TYPE);

        /* Выбираем только активные */
        if($this->active)
        {
            $dbal->join(
                'category',
                CategoryProductInfo::class,
                'info',
                'info.event = category.event AND info.active = true',
            );
        }

        $dbal->leftJoin(
            'category',
            CategoryProductTrans::class,
            'trans',
            'trans.event = category.event AND trans.local = :local',
        );

        /** Свойства конструктора объекта гидрации */
        $dbal->select('category.id AS value');
        $dbal->addSelect('trans.name AS options');
        $dbal->addSelect(
            "JSON_AGG
            (
                JSONB_BUILD_OBJECT
                (
                    'category_url', info.url 
                )
            ) AS params"
        );

        return $dbal
            ->enableCache('products-category', 86400)
            ->fetchHydrate(CategoryProductUid::class);
    }

}
