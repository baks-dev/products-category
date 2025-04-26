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

namespace BaksDev\Products\Category\Repository\VariationByCategory;

use BaksDev\Core\Doctrine\ORMQueryBuilder;
use BaksDev\Products\Category\Entity\Offers\CategoryProductOffers;
use BaksDev\Products\Category\Entity\Offers\Variation\CategoryProductVariation;
use BaksDev\Products\Category\Type\Offers\Id\CategoryProductOffersUid;

final class VariationByOfferRepository implements VariationByOfferInterface
{
    private ?CategoryProductOffersUid $offer = null;

    public function __construct(private readonly ORMQueryBuilder $ORMQueryBuilder) {}

    public function offer(CategoryProductOffers|CategoryProductOffersUid|string $offer): self
    {
        if($offer instanceof CategoryProductOffers)
        {
            $offer = $offer->getId();
        }

        if(is_string($offer))
        {
            $offer = new CategoryProductOffersUid($offer);
        }

        $this->offer = $offer;
        return $this;
    }

    /**
     * Метод получает идентификатор настройки торгового предложения продукта в категории
     */
    public function findCategoryProductVariation(): ?CategoryProductVariation
    {
        $qb = $this->ORMQueryBuilder->createQueryBuilder(self::class);

        $qb->from(CategoryProductOffers::class, 'offer');

        if($this->offer)
        {
            $qb
                ->where('offer.id = :offer')
                ->setParameter(
                    key: 'offer',
                    value: $this->offer,
                    type: CategoryProductOffersUid::TYPE
                );
        }
        $qb
            ->select('variation')
            ->leftJoin(
                CategoryProductVariation::class,
                'variation',
                'WITH',
                'variation.offer = offer.id'
            );


        return $qb->getOneOrNullResult();
    }
}
