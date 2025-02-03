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

declare(strict_types=1);

namespace BaksDev\Products\Category\Repository\SettingsByCategory;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Products\Category\Entity\CategoryProduct;
use BaksDev\Products\Category\Entity\Offers\CategoryProductOffers;
use BaksDev\Products\Category\Entity\Offers\Variation\CategoryProductVariation;
use BaksDev\Products\Category\Entity\Offers\Variation\Modification\CategoryProductModification;
use BaksDev\Products\Category\Type\Id\CategoryProductUid;
use InvalidArgumentException;


final class SettingsByCategoryRepository implements SettingsByCategoryInterface
{
    public function __construct(private readonly DBALQueryBuilder $DBALQueryBuilder) {}

    private CategoryProductUid|false $category = false;

    public function category(CategoryProduct|CategoryProductUid|string $category): self
    {
        if(is_string($category))
        {
            $category = new CategoryProductUid($category);
        }

        if($category instanceof CategoryProduct)
        {
            $category = $category->getId();
        }

        $this->category = $category;

        return $this;
    }

    /**
     * Метод возвращает настройки торговых предложений
     */
    public function find(): array|bool
    {
        $dbal = $this->DBALQueryBuilder->createQueryBuilder(self::class);

        if(false === $this->category)
        {
            throw new InvalidArgumentException('Invalid Argument Category');
        }

        $dbal
            ->from(CategoryProduct::class, 'category')
            ->where('category.id = :category')
            ->setParameter('category', $this->category, CategoryProductUid::TYPE);


        $dbal
            ->addSelect('offer.id AS offer_id')
            ->addSelect('offer.reference AS offer_reference')
            ->addSelect('offer.image AS offer_image')
            ->addSelect('offer.price AS offer_price')
            ->addSelect('offer.article AS offer_article')
            ->leftJoin(
                'category',
                CategoryProductOffers::class,
                'offer',
                'offer.event = category.event'
            );

        $dbal
            ->addSelect('variation.id AS variation_id')
            ->addSelect('variation.reference AS variation_reference')
            ->addSelect('variation.image AS variation_image')
            ->addSelect('variation.price AS variation_price')
            ->addSelect('variation.article AS variation_article')
            ->leftJoin(
                'offer',
                CategoryProductVariation::class,
                'variation',
                'variation.offer = offer.id'
            );

        $dbal
            ->addSelect('modification.id AS modification_id')
            ->addSelect('modification.reference AS modification_reference')
            ->addSelect('modification.image AS modification_image')
            ->addSelect('modification.price AS modification_price')
            ->addSelect('modification.article AS modification_article')
            ->leftJoin(
                'variation',
                CategoryProductModification::class,
                'modification',
                'modification.variation = variation.id'
            );

        return $dbal
            ->enableCache('products-category', '1 day')
            ->fetchAssociative();
    }
}
