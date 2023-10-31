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

namespace BaksDev\Products\Category\Repository\CategoryVariationForm;



use BaksDev\Core\Type\Locale\Locale;
use BaksDev\Products\Category\Entity;
use BaksDev\Products\Category\Type\Id\CategoryUid;
use BaksDev\Products\Category\Type\Offers\Id\ProductCategoryOffersUid;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

//use BaksDev\Products\Category\Type\Offers\Id\OffersUid;

//use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

//use Doctrine\ORM\Query\ResultSetMapping;
//use Doctrine\Persistence\ManagerRegistry;
//use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class CategoryVariationFormRepository implements CategoryVariationFormInterface
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
	
	public function get(ProductCategoryOffersUid $offer) : ?CategoryVariationFormDTO
	{
		//$locale = new Locale($this->translator->getLocale());
		
		$qb = $this->entityManager->createQueryBuilder();
		$select = sprintf(
			'new %s(
            variation.id,
            variation.reference,
            variation.image,
            variation.price,
            variation.quantitative,
            variation.article,
            variation_trans.name,
            
            variation.postfix,
            variation_trans.postfix
        )',
			CategoryVariationFormDTO::class
		);
		
		$qb->select($select);
	
		$qb->from(Entity\Offers\Variation\ProductCategoryVariation::class, 'variation');
		
		$qb->join(Entity\Offers\ProductCategoryOffers::class, 'offer', 'WITH', 'offer.id = variation.offer');
		
		$qb->join(Entity\ProductCategory::class, 'category', 'WITH', 'category.event = offer.event');
		

		$qb->leftJoin(
			Entity\Offers\Variation\Trans\ProductCategoryVariationTrans::class,
			'variation_trans',
			'WITH',
			'variation_trans.variation = variation.id AND variation_trans.local = :locale'
		);
		
		$qb->setParameter('locale', new Locale($this->translator->getLocale()), Locale::TYPE);
		
		$qb->where('offer.id = :offer');
		$qb->setParameter('offer', $offer, ProductCategoryOffersUid::TYPE);
		
		return $qb->getQuery()->getOneOrNullResult();
		
	}
}