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

namespace BaksDev\Products\Category\Repository\AllCategoryByMenu;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Products\Category\Entity as EntityCategory;

final class AllCategoryByMenu implements AllCategoryByMenuInterface
{
    private DBALQueryBuilder $DBALQueryBuilder;

    public function __construct(
        DBALQueryBuilder $DBALQueryBuilder,
    ) {
        $this->DBALQueryBuilder = $DBALQueryBuilder;
    }

    /** Метод возвращает все категории и их вложенные для двухуровнего меню */
    public function fetchAllCatalogMenuAssociative(): array
    {
        $qb = $this->DBALQueryBuilder->createQueryBuilder(self::class);
        $qb->bindLocal();

        /* Категория */
        $qb->select('category.id');
        $qb->addSelect('category.event AS event');
        $qb->from(EntityCategory\ProductCategory::TABLE, 'category');

        /* События категории */
        $qb->addSelect('category_event.sort AS category_sort');
        $qb->addSelect('category_event.parent AS category_parent');

        $qb->join(
            'category',
            EntityCategory\Event\ProductCategoryEvent::TABLE,
            'category_event',
            'category_event.id = category.event AND category_event.parent IS NULL'
        );

        $qb->addSelect('category_info.url AS category_url');
        $qb->join(
            'category_event',
            EntityCategory\Info\ProductCategoryInfo::TABLE,
            'category_info',
            'category_info.event = category.event AND category_info.active = true'
        );

        /* Обложка */
        $qb->addSelect('category_cover.ext AS category_cover_ext');
        $qb->addSelect('category_cover.cdn AS category_cover_cdn');

        $qb->addSelect("
			CASE
			 WHEN category_cover.name IS NOT NULL THEN
					CONCAT ( '/upload/".EntityCategory\Cover\ProductCategoryCover::TABLE."' , '/', category_cover.name)
			   		ELSE NULL
			END AS category_cover_dir
		"
        );
        
        $qb->leftJoin(
            'category_event',
            EntityCategory\Cover\ProductCategoryCover::TABLE,
            'category_cover',
            'category_cover.event = category_event.id'
        );

        /* Перевод категории */
        $qb->addSelect('category_trans.name AS category_name');
        $qb->addSelect('category_trans.description AS category_description');

        $qb->leftJoin(
            'category_event',
            EntityCategory\Trans\ProductCategoryTrans::TABLE,
            'category_trans',
            'category_trans.event = category_event.id AND category_trans.local = :local'
        );

        /* ВЛОЖЕННЫЕ РАЗДЕЛЫ */

        // $qb->addSelect('parent_category_event.id AS parent_event');
        $qb->leftJoin('category',
            EntityCategory\Event\ProductCategoryEvent::TABLE,
            'parent_category_event',
            'parent_category_event.parent = category.id'
        );

        $qb->leftJoin('parent_category_event',
            EntityCategory\Info\ProductCategoryInfo::TABLE,
            'parent_category_info',
            'parent_category_info.event = parent_category_event.id'
        );

        $qb->leftJoin('parent_category_event',
            EntityCategory\Cover\ProductCategoryCover::TABLE,
            'parent_category_cover',
            'parent_category_cover.event = parent_category_event.id'
        );

        // $qb->addSelect('parent_category_trans.name AS parent_category_name');
        $qb->leftJoin('parent_category_event',
            EntityCategory\Trans\ProductCategoryTrans::TABLE,
            'parent_category_trans',
            'parent_category_trans.event = parent_category_event.id  AND parent_category_trans.local = :local'
        );

        $qb->addSelect("JSON_AGG
		( DISTINCT
			
				JSONB_BUILD_OBJECT
				(
					'0', parent_category_event.sort,
					
					'parent_category_url', parent_category_info.url,
					'parent_category_counter', parent_category_info.counter,
					
					'parent_category_cover_name', CASE
												 WHEN parent_category_cover.name IS NOT NULL THEN
														CONCAT ( '/upload/".EntityCategory\Cover\ProductCategoryCover::TABLE."' , '/', parent_category_cover.name)
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

        $qb->orderBy('category_event.sort', 'ASC');

        /* Группировка  */
        $qb->allGroupByExclude();

        /* Кешируем результат DBAL */
        return $qb
            ->enableCache('products-category', 86400)
            ->fetchAllAssociativeIndexed();

    }
}
