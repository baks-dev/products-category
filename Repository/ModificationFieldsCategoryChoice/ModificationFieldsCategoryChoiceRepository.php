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

namespace BaksDev\Products\Category\Repository\ModificationFieldsCategoryChoice;

use BaksDev\Core\Doctrine\ORMQueryBuilder;
use BaksDev\Products\Category\Entity\Offers\Variation\CategoryProductVariation;
use BaksDev\Products\Category\Entity\Offers\Variation\Modification\CategoryProductModification;
use BaksDev\Products\Category\Entity\Offers\Variation\Modification\Trans\CategoryProductModificationTrans;
use BaksDev\Products\Category\Type\Offers\Modification\CategoryProductModificationUid;
use BaksDev\Products\Category\Type\Offers\Variation\CategoryProductVariationUid;

final class ModificationFieldsCategoryChoiceRepository implements ModificationFieldsCategoryChoiceInterface
{
    private string|CategoryProductVariationUid|CategoryProductVariation $variation;

    public function __construct(private readonly ORMQueryBuilder $ORMQueryBuilder) {}

    public function variation(CategoryProductVariation|CategoryProductVariationUid|string $variation): self
    {
        if($variation instanceof CategoryProductVariation)
        {
            $variation = $variation->getId();
        }

        if(is_string($variation))
        {
            $variation = new CategoryProductVariationUid($variation);
        }

        $this->variation = $variation;
        return $this;

    }

    public function findAllModification(): ?CategoryProductModificationUid
    {
        $orm = $this->ORMQueryBuilder
            ->createQueryBuilder(self::class)
            ->bindLocal();

        $select = sprintf(
            'new %s(modification.id, trans.name, modification.reference)',
            CategoryProductModificationUid::class
        );
        $orm->select($select);

        $orm->from(CategoryProductVariation::class, 'variation');

        if($this->variation)
        {
            $orm
                ->where('variation.id = :variation')
                ->setParameter(
                    'variation',
                    $this->variation,
                    CategoryProductVariationUid::TYPE
                );
        }


        $orm->join(
            CategoryProductModification::class,
            'modification',
            'WITH',
            'modification.variation = variation.id'
        );

        $orm->leftJoin(
            CategoryProductModificationTrans::class,
            'trans',
            'WITH',
            'trans.modification = modification.id AND trans.local = :local'
        );


        /* Кешируем результат ORM */
        return $orm->enableCache('products-category', 86400)->getOneOrNullResult();

    }

}
