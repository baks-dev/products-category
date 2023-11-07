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
use BaksDev\Core\Type\Locale\Locale;
use BaksDev\Products\Category\Entity as ProductCategoryEntity;
use Symfony\Contracts\Translation\TranslatorInterface;

final class CategoryByUrl implements CategoryByUrlInterface
{

    private TranslatorInterface $translator;
    private DBALQueryBuilder $DBALQueryBuilder;


    public function __construct(
        DBALQueryBuilder $DBALQueryBuilder,
        TranslatorInterface $translator
    )
    {
        $this->translator = $translator;
        $this->DBALQueryBuilder = $DBALQueryBuilder;
    }


    public function fetchCategoryAssociative(string $url): ?array
    {
        $qb = $this->DBALQueryBuilder->createQueryBuilder(self::class);

        $qb->setParameter('local', new Locale($this->translator->getLocale()), Locale::TYPE);

        $qb->select('info.event  AS category_event')->addGroupBy('info.event');
        $qb->addSelect('info.url AS category_url')->addGroupBy('info.url');
        $qb->addSelect('info.counter AS category_counter')->addGroupBy('info.counter');

        $qb->from(ProductCategoryEntity\Info\ProductCategoryInfo::TABLE, 'info');

        $qb->addSelect('product_category.id AS category_id')->addGroupBy('product_category.id');
        $qb->join('info',
            ProductCategoryEntity\ProductCategory::TABLE,
            'product_category',
            'product_category.event = info.event'
        );


        $qb->addSelect('product_category_event.parent AS category_parent')->addGroupBy('product_category_event.parent');
        $qb->join('product_category',
            ProductCategoryEntity\Event\ProductCategoryEvent::TABLE,
            'product_category_event',
            'product_category_event.id = product_category.event'
        );

        $qb->addSelect('product_category_trans.name AS category_name')->addGroupBy('product_category_trans.name');

        $qb->leftJoin('product_category',
            ProductCategoryEntity\Trans\ProductCategoryTrans::TABLE,
            'product_category_trans',
            'product_category_trans.event = product_category_event.id  AND product_category_trans.local = :local'
        );


        $qb->addSelect('product_category_landing.header AS category_header')->addGroupBy('product_category_landing.header');
        $qb->addSelect('product_category_landing.bottom AS category_bottom')->addGroupBy('product_category_landing.bottom');

        $qb->leftJoin('product_category',
            ProductCategoryEntity\Landing\ProductCategoryLanding::TABLE,
            'product_category_landing',
            'product_category_landing.event = product_category_event.id  AND product_category_landing.local = :local'
        );



        /* КОРНЕВОЙ РАЗДЕЛ */

        $qb->leftJoin('product_category_event',
            ProductCategoryEntity\ProductCategory::TABLE,
            'parent_product_category',
            'parent_product_category.id = product_category_event.parent'
        );

        $qb->addSelect('parent_product_category_trans.name AS parent_category_name')->addGroupBy('parent_product_category_trans.name');
        $qb->leftJoin('parent_product_category',
            ProductCategoryEntity\Trans\ProductCategoryTrans::TABLE,
            'parent_product_category_trans',
            'parent_product_category_trans.event = parent_product_category.event AND parent_product_category_trans.local = :local'
        );

        $qb->addSelect('parent_product_category_info.url AS parent_category_url')->addGroupBy('parent_product_category_info.url');
        $qb->addSelect('parent_product_category_info.counter AS parent_category_counter')->addGroupBy('parent_product_category_info.counter');
        $qb->leftJoin('parent_product_category',
            ProductCategoryEntity\Info\ProductCategoryInfo::TABLE,
            'parent_product_category_info',
            'parent_product_category_info.event = parent_product_category.event '
        );


        /* ВЛОЖЕННЫЕ РАЗДЕЛЫ */


        //$qb->addSelect('parent_category_event.id AS parent_event');
        $qb->leftJoin('product_category',
            ProductCategoryEntity\Event\ProductCategoryEvent::TABLE,
            'parent_category_event',
            'parent_category_event.parent = product_category.id'
        );

        $qb->leftJoin('parent_category_event',
            ProductCategoryEntity\Info\ProductCategoryInfo::TABLE,
            'parent_category_info',
            'parent_category_info.event = parent_category_event.id'
        );

        $qb->leftJoin('parent_category_event',
            ProductCategoryEntity\Cover\ProductCategoryCover::TABLE,
            'parent_category_cover',
            'parent_category_cover.event = parent_category_event.id'
        );


        //$qb->addSelect('parent_category_trans.name AS parent_category_name');
        $qb->leftJoin('parent_category_event',
            ProductCategoryEntity\Trans\ProductCategoryTrans::TABLE,
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
														CONCAT ( '/upload/".ProductCategoryEntity\Cover\ProductCategoryCover::TABLE."' , '/', parent_category_cover.name)
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


        $qb->andWhere('info.url = :url');
        $qb->andWhere('info.active = true');
        $qb->setParameter('url', $url);


        /* Кешируем результат DBAL */
        return $qb
            ->enableCache('products-category', 86400)
            ->fetchAssociative();

    }

}