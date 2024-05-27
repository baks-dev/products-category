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

namespace BaksDev\Products\Category\Repository\CategoryModificationForm;

use BaksDev\Core\Type\Locale\Locale;
use BaksDev\Products\Category\Entity;
use BaksDev\Products\Category\Type\Offers\Variation\ProductCategoryVariationUid;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class CategoryModificationForm implements CategoryModificationFormInterface
{
	private EntityManagerInterface $entityManager;
	
	private TranslatorInterface $translator;
	
	
	public function __construct(
		EntityManagerInterface $entityManager,
		TranslatorInterface $translator,
	)
	{
		$this->entityManager = $entityManager;
		$this->translator = $translator;
	}
	
	public function get(ProductCategoryVariationUid $variation) : ?CategoryModificationFormDTO
	{
		//$locale = new Locale($this->translator->getLocale());
		
		$qb = $this->entityManager->createQueryBuilder();
		$select = sprintf(
			'new %s(
            modification.id,
            modification.reference,
            modification.image,
            modification.price,
            modification.quantitative,
            modification.article,
            modification_trans.name,
            
            modification.postfix,
            modification_trans.postfix
            
        )',
			CategoryModificationFormDTO::class
		);
		
		$qb->select($select);
		//$qb->select('offers');
		
		$qb->from(Entity\Offers\Variation\Modification\ProductCategoryModification::class, 'modification');
		
		$qb->join(Entity\Offers\Variation\ProductCategoryVariation::class, 'variation', 'WITH', 'variation.id = modification.variation');
		
		$qb->join(Entity\Offers\ProductCategoryOffers::class, 'offer', 'WITH', 'offer.id = variation.offer');
		
		$qb->join(Entity\ProductCategory::class, 'category', 'WITH', 'category.event = offer.event');
		
		$qb->leftJoin(
			Entity\Offers\Variation\Modification\Trans\ProductCategoryModificationTrans::class,
			'modification_trans',
			'WITH',
			'modification_trans.modification = modification.id AND modification_trans.local = :locale'
		);
		
		$qb->setParameter('locale', new Locale($this->translator->getLocale()), Locale::TYPE);
		
		$qb->where('variation.id = :variation');
		$qb->setParameter('variation', $variation, ProductCategoryVariationUid::TYPE);
		
		//dd($result);
		
		return $qb->getQuery()->getOneOrNullResult();
		
	}
	
}