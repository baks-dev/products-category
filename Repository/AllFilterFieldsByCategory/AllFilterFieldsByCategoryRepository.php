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

namespace BaksDev\Products\Category\Repository\AllFilterFieldsByCategory;

use BaksDev\Core\Type\Locale\Locale;
use BaksDev\Products\Category\Entity as ProductCategoryEntity;
use BaksDev\Products\Category\Type\Id\ProductCategoryUid;
use Doctrine\DBAL\Connection;
use Symfony\Contracts\Translation\TranslatorInterface;

final class AllFilterFieldsByCategoryRepository implements AllFilterFieldsByCategoryInterface
{
	
	private Connection $connection;
	
	private Locale $local;
	
	private TranslatorInterface $translator;
	
	
	public function __construct(
		Connection $connection,
		TranslatorInterface $translator
	)
	{
	
		$this->connection = $connection;
		$this->translator = $translator;
	}
	
	/** Метод возвращает все свойства, учавствующие в фильтре */
	
	public function fetchAllFilterCategoryFieldsAssociative(ProductCategoryUid $category) : array
	{
		$qb = $this->connection->createQueryBuilder();
		
		$qb->setParameter('local',  new Locale($this->translator->getLocale()), Locale::TYPE);
		
		$qb->from(ProductCategoryEntity\ProductCategory::TABLE, 'category');
		
		$qb->join('category',
			ProductCategoryEntity\Event\ProductCategoryEvent::TABLE,
			'category_event',
			'category_event.id = category.event'
		);
		
		$qb->leftJoin('category_event',
			ProductCategoryEntity\Section\ProductCategorySection::TABLE,
			'category_section',
			'category_section.event = category_event.id'
		);
		
		$qb->select('category_section_field.id');
		$qb->addSelect('category_section_field.type');
		$qb->leftJoin('category_section',
			ProductCategoryEntity\Section\Field\ProductCategorySectionField::TABLE,
			'category_section_field',
			'category_section_field.section = category_section.id AND category_section_field.filter = TRUE'
		);
		
		
		$qb->addSelect('category_section_field_trans.name');
		$qb->leftJoin('category_section_field',
			ProductCategoryEntity\Section\Field\Trans\ProductCategorySectionFieldTrans::TABLE,
			'category_section_field_trans',
			'category_section_field_trans.field = category_section_field.id AND category_section_field_trans.local = :local'
		);
		
		
		$qb->where('category.id = :category');
		$qb->setParameter('category', $category);
		
		$qb->orderBy('category_section.sort, category_section_field.sort');
		
		return $qb->fetchAllAssociative();
	}
	
}