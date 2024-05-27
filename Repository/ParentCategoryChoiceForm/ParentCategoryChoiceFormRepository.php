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

namespace BaksDev\Products\Category\Repository\ParentCategoryChoiceForm;

use BaksDev\Products\Category\Entity as EntityCategory;
use BaksDev\Core\Type\Locale\Locale;
use BaksDev\Products\Category\Type\Id\ProductCategoryUid;
use BaksDev\Products\Category\Type\Parent\ProductParentCategoryUid;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/** Клас получает массив категорий для формы и перобразует названия (path) согласно вложенности */
final class ParentCategoryChoiceFormRepository implements ParentCategoryChoiceFormInterface
{
	
	private EntityManagerInterface $entityManager;
	
	private TranslatorInterface $translator;
	
	
	public function __construct(EntityManagerInterface $entityManager, TranslatorInterface $translator)
	{
		$this->entityManager = $entityManager;
		
		$this->translator = $translator;
	}
	
	public function get(?ProductCategoryUid $categoryUid = null) : array
	{
		
		$qb = $this->entityManager->createQueryBuilder();
		
		$select = sprintf('new %s(category.id, trans.name, event.parent)', ProductParentCategoryUid::class);
		
		$qb->select($select);
		

		$qb->from(EntityCategory\ProductCategory::class, 'category', 'category.id');
		
		$qb->join(
			EntityCategory\Event\ProductCategoryEvent::class,
			'event',
			'WITH',
			'event.id = category.event AND event.category = category.id'
		);
		
		$qb->leftJoin(
			EntityCategory\Trans\ProductCategoryTrans::class,
			'trans',
			'WITH',
			'trans.event = event.id AND trans.local = :locale'
		);
		
		$qb->setParameter('locale', new Locale($this->translator->getLocale()), Locale::TYPE);
		
		$qb->orderBy(
			'CASE
        WHEN
            event.parent IS NULL
        THEN
            category.id
        ELSE
            event.parent
        END',
			'DESC'
		);
		
		
		return $qb->getQuery()->getResult();
	}
	
}