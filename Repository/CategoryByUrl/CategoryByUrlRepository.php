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

namespace BaksDev\Products\Category\Repository\CategoryByUrl;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Products\Category\Entity\CategoryProduct;
use BaksDev\Products\Category\Entity\Cover\CategoryProductCover;
use BaksDev\Products\Category\Entity\Event\CategoryProductEvent;
use BaksDev\Products\Category\Entity\Info\CategoryProductInfo;
use BaksDev\Products\Category\Entity\Landing\CategoryProductLanding;
use BaksDev\Products\Category\Entity\Trans\CategoryProductTrans;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class CategoryByUrlRepository implements CategoryByUrlInterface
{
    public function __construct(private DBALQueryBuilder $DBALQueryBuilder) {}


    /**
     *  Категория по части URI
     */
    public function findByUrl(string $url): array|false
    {
        $dbal = $this
            ->DBALQueryBuilder->createQueryBuilder(self::class)
            ->bindLocal();

        $dbal
            ->select('info.event AS category_event')
            ->addSelect('info.url AS category_url')
            ->addSelect('info.counter AS category_counter')
            ->from(CategoryProductInfo::class, 'info')
            ->where('info.url = :url')
            ->andWhere('info.active = true')
            ->setParameter('url', $url);


        $dbal
            ->addSelect('product_category.id AS category_id')
            ->join(
                'info',
                CategoryProduct::class,
                'product_category',
                'product_category.event = info.event'
            );


        $dbal
            ->addSelect('product_category_event.parent AS category_parent')
            ->leftJoin(
                'product_category',
                CategoryProductEvent::class,
                'product_category_event',
                'product_category_event.id = product_category.event'
            );

        $dbal
            ->addSelect('product_category_trans.name AS category_name')
            ->leftJoin(
                'product_category',
                CategoryProductTrans::class,
                'product_category_trans',
                'product_category_trans.event = product_category_event.id  AND product_category_trans.local = :local'
            );


        $dbal
            ->addSelect('product_category_landing.header AS category_header')
            ->addSelect('product_category_landing.bottom AS category_bottom')
            ->leftJoin(
                'product_category',
                CategoryProductLanding::class,
                'product_category_landing',
                'product_category_landing.event = product_category_event.id  AND product_category_landing.local = :local'
            );


        /* КОРНЕВОЙ РАЗДЕЛ */

        $dbal
            ->leftJoin(
                'product_category_event',
                CategoryProduct::class,
                'parent_product_category',
                'parent_product_category.id = product_category_event.parent'
            );

        $dbal
            ->addSelect('parent_product_category_trans.name AS parent_category_name')
            ->leftJoin(
                'parent_product_category',
                CategoryProductTrans::class,
                'parent_product_category_trans',
                'parent_product_category_trans.event = parent_product_category.event AND parent_product_category_trans.local = :local'
            );

        $dbal
            ->addSelect('parent_product_category_info.url AS parent_category_url')
            ->addSelect('parent_product_category_info.counter AS parent_category_counter')
            ->leftJoin(
                'parent_product_category',
                CategoryProductInfo::class,
                'parent_product_category_info',
                'parent_product_category_info.event = parent_product_category.event '
            );


        /* ВЛОЖЕННЫЕ РАЗДЕЛЫ */


        $dbal->leftJoin(
            'product_category',
            CategoryProductEvent::class,
            'parent_category_event',
            'parent_category_event.parent = product_category.id'
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


        //$dbal->addSelect('parent_category_trans.name AS parent_category_name');
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

        $dbal->allGroupByExclude();

        return $dbal
            ->enableCache('products-category', 86400)
            ->fetchAssociative() ?: false;

    }
}
