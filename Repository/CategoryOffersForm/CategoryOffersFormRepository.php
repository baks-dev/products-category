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

namespace BaksDev\Products\Category\Repository\CategoryOffersForm;

use BaksDev\Core\Doctrine\ORMQueryBuilder;
use BaksDev\Products\Category\Entity\CategoryProduct;
use BaksDev\Products\Category\Entity\Offers\CategoryProductOffers;
use BaksDev\Products\Category\Entity\Offers\Trans\CategoryProductOffersTrans;
use BaksDev\Products\Category\Type\Id\CategoryProductUid;

final class CategoryOffersFormRepository implements CategoryOffersFormInterface
{
    private ?CategoryProductUid $category = null;

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

    public function findAllOffers(): ?CategoryOffersFormDTO
    {
        $orm = $this->ORMQueryBuilder
            ->createQueryBuilder(self::class)
            ->bindLocal();

        $select = sprintf(
            'new %s(
            offers.id,
            offers.reference,
            offers.image,
            offers.price,
            offers.quantitative,
            offers.article,
            offers_trans.name,
            
            offers.postfix,
            offers_trans.postfix
        )',
            CategoryOffersFormDTO::class
        );

        $orm->select($select);

        $orm->from(CategoryProductOffers::class, 'offers');

        if($this->category)
        {
            $orm
                ->join(
                    CategoryProduct::class,
                    'category',
                    'WITH',
                    'category.event = offers.event'
                )
                ->where('category.id = :category')
                ->setParameter(
                    key: 'category',
                    value: $this->category,
                    type: CategoryProductUid::TYPE
                );
        }

        $orm
            ->leftJoin(
                CategoryProductOffersTrans::class,
                'offers_trans',
                'WITH',
                'offers_trans.offer = offers.id AND offers_trans.local = :local'
            );

        return $orm->getOneOrNullResult();

    }

}
