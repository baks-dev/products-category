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

namespace BaksDev\Products\Category\Repository\AllCategoryByMenu;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Products\Category\Entity\CategoryProduct;
use BaksDev\Products\Category\Entity\Cover\CategoryProductCover;
use BaksDev\Products\Category\Entity\Event\CategoryProductEvent;
use BaksDev\Products\Category\Entity\Info\CategoryProductInfo;
use BaksDev\Products\Category\Entity\Trans\CategoryProductTrans;
use BaksDev\Products\Product\Entity\Category\ProductCategory;
use BaksDev\Products\Product\Entity\Info\ProductInfo;
use BaksDev\Products\Product\Entity\Offers\Image\ProductOfferImage;
use BaksDev\Products\Product\Entity\Offers\ProductOffer;
use BaksDev\Products\Product\Entity\Offers\Quantity\ProductOfferQuantity;
use BaksDev\Products\Product\Entity\Offers\Variation\Image\ProductVariationImage;
use BaksDev\Products\Product\Entity\Offers\Variation\Modification\Image\ProductModificationImage;
use BaksDev\Products\Product\Entity\Offers\Variation\Modification\ProductModification;
use BaksDev\Products\Product\Entity\Offers\Variation\Modification\Quantity\ProductModificationQuantity;
use BaksDev\Products\Product\Entity\Offers\Variation\ProductVariation;
use BaksDev\Products\Product\Entity\Offers\Variation\Quantity\ProductVariationQuantity;
use BaksDev\Products\Product\Entity\Photo\ProductPhoto;
use BaksDev\Products\Product\Entity\Price\ProductPrice;
use BaksDev\Products\Product\Entity\Product;
use BaksDev\Products\Product\Entity\Trans\ProductTrans;

final class AllCategoryByMenuRepository implements AllCategoryByMenuInterface
{
    public function __construct(private readonly DBALQueryBuilder $DBALQueryBuilder) {}

    /**
     * Метод возвращает все категории и их вложенные для двухуровневого меню
     */
    public function findAll(): array
    {
        $dbal = $this->DBALQueryBuilder
            ->createQueryBuilder(self::class)
            ->bindLocal();

        /* Категория */
        $dbal
            ->select('category.id')
            ->addSelect('category.event AS event')
            ->from(CategoryProduct::class, 'category');

        /* События категории */
        $dbal
            ->addSelect('category_event.sort AS category_sort')
            ->addSelect('category_event.parent AS category_parent')
            ->join(
                'category',
                CategoryProductEvent::class,
                'category_event',
                'category_event.id = category.event AND category_event.parent IS NULL'
            );

        $dbal
            ->addSelect('category_info.url AS category_url')
            ->join(
                'category_event',
                CategoryProductInfo::class,
                'category_info',
                'category_info.event = category.event AND category_info.active = true'
            );

        /* Обложка */
        $dbal
            ->addSelect('category_cover.ext AS category_cover_ext')
            ->addSelect('category_cover.cdn AS category_cover_cdn')
            ->addSelect(
                "
			CASE
			 WHEN category_cover.name IS NOT NULL THEN
					CONCAT ( '/upload/".$dbal->table(CategoryProductCover::class)."' , '/', category_cover.name)
			   		ELSE NULL
			END AS category_cover_dir
		"
            );

        $dbal
            ->leftJoin(
                'category_event',
                CategoryProductCover::class,
                'category_cover',
                'category_cover.event = category_event.id'
            );

        /* Перевод категории */
        $dbal
            ->addSelect('category_trans.name AS category_name')
            ->addSelect('category_trans.description AS category_description')
            ->leftJoin(
                'category_event',
                CategoryProductTrans::class,
                'category_trans',
                'category_trans.event = category_event.id AND category_trans.local = :local'
            );

        /* ВЛОЖЕННЫЕ РАЗДЕЛЫ */

        // $dbal->addSelect('parent_category_event.id AS parent_event');
        $dbal->leftJoin(
            'category',
            CategoryProductEvent::class,
            'parent_category_event',
            'parent_category_event.parent = category.id'
        );

        $dbal->leftJoin(
            'parent_category_event',
            CategoryProductInfo::class,
            'parent_category_info',
            'parent_category_info.event = parent_category_event.id'
        );

        $dbal->leftJoin(
            'parent_category_event',
            CategoryProductCover::class,
            'parent_category_cover',
            'parent_category_cover.event = parent_category_event.id'
        );

        // $dbal->addSelect('parent_category_trans.name AS parent_category_name');
        $dbal->leftJoin(
            'parent_category_event',
            CategoryProductTrans::class,
            'parent_category_trans',
            'parent_category_trans.event = parent_category_event.id  AND parent_category_trans.local = :local'
        );

        $dbal->addSelect(
            "JSON_AGG
		( DISTINCT
			
				JSONB_BUILD_OBJECT
				(
					'0', parent_category_event.sort,
					
					'parent_category_url', parent_category_info.url,
					'parent_category_counter', parent_category_info.counter,
					
					'parent_category_cover_name', 
					CASE 
					    WHEN parent_category_cover.name IS NOT NULL 
					    THEN CONCAT ( '/upload/".$dbal->table(CategoryProductCover::class)."' , '/', parent_category_cover.name)
					    ELSE NULL
					END,
					
					'parent_category_cover_ext', parent_category_cover.ext,
					'parent_category_cover_cdn', parent_category_cover.cdn,
		
					'parent_category_event', parent_category_event.id,
					'parent_category_name', parent_category_trans.name
				)
		)
			AS parent_category"
        );

        $dbal->orderBy('category_event.sort', 'ASC');


        $dbal->allGroupByExclude();

        return $dbal
            ->enableCache('products-category', 86400, false)
            ->fetchAllAssociativeIndexed();

    }


}
