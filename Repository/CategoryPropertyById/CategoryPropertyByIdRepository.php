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

namespace BaksDev\Products\Category\Repository\CategoryPropertyById;

use BaksDev\Products\Category\Entity;
use BaksDev\Core\Type\Locale\Locale;
use BaksDev\Products\Category\Type\Id\ProductCategoryUid;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class CategoryPropertyByIdRepository implements CategoryPropertyByIdInterface
{

	private EntityManagerInterface $entityManager;
	
	private TranslatorInterface $translator;
	
	
	public function __construct(EntityManagerInterface $entityManager, TranslatorInterface $translator)
	{

		$this->entityManager = $entityManager;
		$this->translator = $translator;
	}
	
	public function get(ProductCategoryUid $categoryUid) : ?array
	{
		
		$qb = $this->entityManager->createQueryBuilder();
		
		
		$select = sprintf(
			'
          NEW %s(
              section.id,
              section_trans.name,
              field.id,
              field_trans.name,
              field.type,
              field.required,
              field_trans.description
          )',
			CategoryPropertyDTO::class
		);
		
		$qb->select($select);
		
		
		$qb->from(Entity\ProductCategory::class, 'category');
		$qb->join(
			Entity\Event\ProductCategoryEvent::class,
			'category_event',
			'WITH',
			'category_event.id = category.event'
		);
		
		/* Секции свойств */
		$qb->join(
			Entity\Section\ProductCategorySection::class,
			'section',
			'WITH',
			'  section.event = category_event.id'
		);
		
		/* Перевод секции */
		$qb->join(
			Entity\Section\Trans\ProductCategorySectionTrans::class,
			'section_trans',
			'WITH',
			'section_trans.section = section.id AND section_trans.local = :locale'
		);
		
		
		/* Перевод полей */
		//$qb->addSelect('field.id');
		$qb->join(
			Entity\Section\Field\ProductCategorySectionField::class,
			'field',
			'WITH',
			'field.section = section.id'
		);
		
		
		$qb->join(
			Entity\Section\Field\Trans\ProductCategorySectionFieldTrans::class,
			'field_trans',
			'WITH',
			'field_trans.field = field.id AND field_trans.local = :locale'
		);
		
		$qb->setParameter('locale', new Locale($this->translator->getLocale()), Locale::TYPE);
		
		$qb->where('category.id = :category');
		$qb->setParameter('category', $categoryUid, ProductCategoryUid::TYPE);
		
		$qb->orderBy('section.sort', 'ASC');
		$qb->addOrderBy('field.sort', 'ASC');
		
		
		/* Преобразуем ключи */
		$dto = null;
		foreach($qb->getQuery()->getResult() as $item)
		{
			$dto[(string) $item->fieldUid] = $item;
		}
		
		return $dto;
	}
	
}