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

namespace BaksDev\Products\Category\Repository\CategoryOffersForm;

//use App\Module\Delivery\Entity\Delivery;
//use App\Module\Product\Entity\Category;
//use App\Module\Product\Entity\Category\Offers;
//use App\Module\Product\Type\Category\Id\CategoryUid;
//use BaksDev\UsersLevel\Type\Level\Event\LevelEvent;
//use BaksDev\UsersProfile\Entity\Profile;
//use BaksDev\UsersProfile\Type\Profile\Id\ProfileUid;
//use BaksDev\UsersProfile\Type\Profile\Id\ProfileUidType;
//use BaksDev\Core\Type\Locale\Locales;

use BaksDev\Products\Category\Entity;
use BaksDev\Products\Category\Type\Id\CategoryUid;

//use BaksDev\Products\Category\Type\Offers\Id\OffersUid;
use BaksDev\Core\Type\Locale\Locale;

//use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use BaksDev\Products\Category\Type\Id\ProductCategoryUid;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;

//use Doctrine\ORM\Query\ResultSetMapping;
//use Doctrine\Persistence\ManagerRegistry;
//use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class CategoryOffersFormRepository implements CategoryOffersFormInterface
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
	
	public function get(ProductCategoryUid $category) : ?CategoryOffersFormDTO
	{
	
		$qb = $this->entityManager->createQueryBuilder();
		
		$select = sprintf(
			'new %s(
            offers.id,
            offers.reference,
            offers.image,
            offers.price,
            offers.quantitative,
            offers.article,
            offers_trans.name,
            
            offers.postfix,
            offers_trans.postfix
        )',
			CategoryOffersFormDTO::class
		);
		
		$qb->select($select);
		//$qb->select('offers');
		
		$qb->from(Entity\Offers\ProductCategoryOffers::class, 'offers');
		$qb->join(Entity\ProductCategory::class, 'category', 'WITH', 'category.event = offers.event');
		
		//$qb->from(Entity\Category::class, 'category');
		
		//$qb->join(Entity\Offers\Offers::class, 'offers', 'WITH', '  offers.event = category.event');
		
		$qb->leftJoin(
			Entity\Offers\Trans\ProductCategoryOffersTrans::class,
			'offers_trans',
			'WITH',
			'offers_trans.offer = offers.id AND offers_trans.local = :locale'
		);
		
		$qb->setParameter('locale', new Locale($this->translator->getLocale()), Locale::TYPE);
		
		$qb->where('category.id = :category');
		$qb->setParameter('category', $category, ProductCategoryUid::TYPE);
		
		//dd($result);
		
		return $qb->getQuery()->getOneOrNullResult();
		
	}

}