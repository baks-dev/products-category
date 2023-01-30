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

namespace BaksDev\Products\Category\Repository\PropertyFieldsByCategoryChoiceForm;

use BaksDev\Products\Category\Repository\CategoryPropertyById\CategoryPropertyDTO;
use BaksDev\Products\Category\Type\Id\CategoryUid;
use BaksDev\Products\Category\Type\Section\Field\Id\FieldUid;
use BaksDev\Core\Type\Locale\Locale;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\Translation\TranslatorInterface;
use BaksDev\Products\Category\Entity;

final class PropertyFieldsByCategoryChoiceRepository implements PropertyFieldsByCategoryChoiceFormInterface
{
	
	private Locale $local;
	private EntityManagerInterface $entityManager;
	
	public function __construct(EntityManagerInterface $entityManager, TranslatorInterface $translator)
	{
		$this->local = new Locale($translator->getLocale());
		$this->entityManager = $entityManager;
	}
	
	public function get(CategoryUid $categoryUid)
	{
		$qb = $this->entityManager->createQueryBuilder();
		
		
		$select = sprintf(
			'
          NEW %s(
              field.id,
              field_trans.name
          )',
			FieldUid::class
		);
		
		$qb->select($select);
		
		
		$qb->from(Entity\Category::class, 'category');
		$qb->join(Entity\Event\Event::class, 'category_event', 'WITH', 'category_event.id = category.event');
		
		/* Секции свойств */
		$qb->join(Entity\Section\Section::class, 'section', 'WITH', '  section.event = category_event.id');
		
		/* Перевод секции */
		$qb->join(
			Entity\Section\Trans\Trans::class,
			'section_trans',
			'WITH',
			'section_trans.section = section.id AND section_trans.local = :locale'
		);
		
		
		/* Перевод полей */
		//$qb->addSelect('field.id');
		$qb->join(
			Entity\Section\Field\Field::class,
			'field',
			'WITH',
			'field.section = section.id'
		);
		
		
		$qb->join(
			Entity\Section\Field\Trans\Trans::class,
			'field_trans',
			'WITH',
			'field_trans.field = field.id AND field_trans.local = :locale'
		);
		
		$qb->setParameter('locale', $this->local, Locale::TYPE);
		
		$qb->where('category.id = :category');
		$qb->setParameter('category', $categoryUid, CategoryUid::TYPE);
		
		$qb->orderBy('section.sort', 'ASC');
		$qb->addOrderBy('field.sort', 'ASC');
		
		return $qb->getQuery()->getResult();
	}
	
}