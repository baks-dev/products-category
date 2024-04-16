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

declare(strict_types=1);

namespace BaksDev\Products\Category\Repository\CategoryModificationForm;

use BaksDev\Core\Doctrine\ORMQueryBuilder;
use BaksDev\Products\Category\Entity\Offers\CategoryProductOffers;
use BaksDev\Products\Category\Entity\Offers\Variation\Modification\CategoryProductModification;
use BaksDev\Products\Category\Entity\Offers\Variation\Modification\Trans\CategoryProductModificationTrans;
use BaksDev\Products\Category\Entity\Offers\Variation\CategoryProductVariation;
use BaksDev\Products\Category\Entity\CategoryProduct;
use BaksDev\Products\Category\Type\Offers\Variation\CategoryProductVariationUid;

final class CategoryModificationFormRepository implements CategoryModificationFormInterface
{
    private ORMQueryBuilder $ORMQueryBuilder;

    public function __construct(ORMQueryBuilder $ORMQueryBuilder)
    {
        $this->ORMQueryBuilder = $ORMQueryBuilder;
    }

    public function findByVariation(
        CategoryProductVariation|CategoryProductVariationUid|string $variation
    ): ?CategoryModificationFormDTO
    {
        if($variation instanceof CategoryProductVariation)
        {
            $variation = $variation->getId();
        }

        if(is_string($variation))
        {
            $variation = new CategoryProductVariationUid($variation);
        }

        $qb = $this->ORMQueryBuilder
            ->createQueryBuilder(self::class)
            ->bindLocal();

        $select = sprintf(
            'new %s(
            modification.id,
            modification.reference,
            modification.image,
            modification.price,
            modification.quantitative,
            modification.article,
            modification_trans.name,
            
            modification.postfix,
            modification_trans.postfix
            
        )',
            CategoryModificationFormDTO::class
        );

        $qb->select($select);
        //$qb->select('offers');

        $qb
            ->from(CategoryProductModification::class, 'modification')
            ->where('modification.variation = :variation')
            ->setParameter('variation', $variation, CategoryProductVariationUid::TYPE);


        $qb
            ->join(
                CategoryProductVariation::class,
                'variation',
                'WITH',
                'variation.id = modification.variation'
            );

        $qb
            ->join(
                CategoryProductOffers::class,
                'offer',
                'WITH',
                'offer.id = variation.offer'
            );

        $qb
            ->join(
                CategoryProduct::class,
                'category',
                'WITH',
                'category.event = offer.event'
            );

        $qb
            ->leftJoin(
                CategoryProductModificationTrans::class,
                'modification_trans',
                'WITH',
                'modification_trans.modification = modification.id AND 
                modification_trans.local = :local'
            );


        //dd($result);

        return $qb->getQuery()->getOneOrNullResult();

    }

}