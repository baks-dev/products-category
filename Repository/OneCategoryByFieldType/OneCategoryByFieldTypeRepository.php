<?php
/*
 * Copyright 2026.  Baks.dev <admin@baks.dev>
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

declare(strict_types=1);

namespace BaksDev\Products\Category\Repository\OneCategoryByFieldType;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Core\Type\Field\InputField;
use BaksDev\Products\Category\Entity\CategoryProduct;
use BaksDev\Products\Category\Entity\Offers\CategoryProductOffers;
use BaksDev\Products\Category\Type\Id\CategoryProductUid;

final readonly class OneCategoryByFieldTypeRepository implements OneCategoryByFieldTypeInterface
{
    public function __construct(private DBALQueryBuilder $DBALQueryBuilder) {}


    /**
     * Метод возвращает идентификатор одной категории, тип торгового предложения которой был передан в качестве
     * аргумента
     */
    public function find(InputField $type): CategoryProductUid|false
    {
        $dbal = $this->DBALQueryBuilder->createQueryBuilder(self::class);

        $dbal
            ->from(CategoryProductOffers::class, 'product_category_offers')
            ->where('product_category_offers.reference = :type')
            ->setParameter('type', $type,InputField::TYPE);

        $dbal
            ->select('product_category.id as value')
            ->join(
                'product_category_offers',
                CategoryProduct::class,
                'product_category',
                'product_category.event = product_category_offers.event'
            );

        return $dbal->fetchHydrate(CategoryProductUid::class);
    }
}