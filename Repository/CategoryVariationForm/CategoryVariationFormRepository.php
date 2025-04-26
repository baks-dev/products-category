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

namespace BaksDev\Products\Category\Repository\CategoryVariationForm;

use BaksDev\Core\Doctrine\ORMQueryBuilder;
use BaksDev\Products\Category\Entity\CategoryProduct;
use BaksDev\Products\Category\Entity\Offers\CategoryProductOffers;
use BaksDev\Products\Category\Entity\Offers\Variation\CategoryProductVariation;
use BaksDev\Products\Category\Entity\Offers\Variation\Trans\CategoryProductVariationTrans;
use BaksDev\Products\Category\Type\Offers\Id\CategoryProductOffersUid;

final class CategoryVariationFormRepository implements CategoryVariationFormInterface
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

    public function findAllVariation(): ?CategoryVariationFormDTO
    {
        $orm = $this->ORMQueryBuilder
            ->createQueryBuilder(self::class)
            ->bindLocal();

        $select = sprintf(
            'new %s(
            variation.id,
            variation.reference,
            variation.image,
            variation.price,
            variation.quantitative,
            variation.article,
            variation_trans.name,
            
            variation.postfix,
            variation_trans.postfix
        )',
            CategoryVariationFormDTO::class
        );

        $orm->select($select);

        $orm
            ->from(CategoryProductVariation::class, 'variation');

        if($this->offer)
        {
            $orm
                ->where('variation.offer = :offer')
                ->setParameter(
                    key: 'offer',
                    value: $this->offer,
                    type: CategoryProductOffersUid::TYPE
                );
        }

        $orm->join(
            CategoryProductOffers::class,
            'offer',
            'WITH',
            'offer.id = variation.offer'
        );

        $orm->join(
            CategoryProduct::class,
            'category',
            'WITH',
            'category.event = offer.event'
        );


        $orm->leftJoin(
            CategoryProductVariationTrans::class,
            'variation_trans',
            'WITH',
            'variation_trans.variation = variation.id AND variation_trans.local = :local'
        );


        return $orm->getOneOrNullResult();

    }
}
