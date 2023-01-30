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
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;

//use Doctrine\ORM\Query\ResultSetMapping;
//use Doctrine\Persistence\ManagerRegistry;
//use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class CategoryOffersFormRepository implements CategoryOffersFormInterface
{
	//    private ArrayCollection $delivery;
	//    private ArrayCollection $trans;
	//    private ArrayCollection $price;
	//    private ArrayCollection $currency;
	//
	//    private TokenStorageInterface $token;
	//    private ArrayCollection $type;
	
	//private ArrayCollection $offers;
	//private ArrayCollection $trans;
	
	// private TranslatorInterface $translator;
	private EntityManagerInterface $entityManager;
	private Locale $local;
	
	public function __construct(
		EntityManagerInterface $entityManager,
		TranslatorInterface $translator,
	)
	{
		$this->local = new Locale($translator->getLocale());
		$this->entityManager = $entityManager;
	}
	
	public function get(CategoryUid $category)
	{
		//$locale = new Locale($this->translator->getLocale());
		
		$qb = $this->entityManager->createQueryBuilder();
		$select = sprintf(
			'offers, new %s(
            offers.id,
            offers.reference,
            offers.image,
            offers.price,
            offers.quantitative,
            offers.article,
            offers.multiple,
            offers_trans.name
        )',
			CategoryOffersFormDTO::class
		);
		
		$qb->select($select);
		//$qb->select('offers');
		
		$qb->from(Entity\Offers\Offers::class, 'offers', 'offers');
		$qb->join(Entity\Category::class, 'category', 'WITH', 'category.event = offers.event');
		
		//$qb->from(Entity\Category::class, 'category');
		
		//$qb->join(Entity\Offers\Offers::class, 'offers', 'WITH', '  offers.event = category.event');
		
		$qb->join(
			Entity\Offers\Trans\Trans::class,
			'offers_trans',
			'WITH',
			'offers_trans.offer = offers.id AND offers_trans.local = :locale'
		);
		
		$qb->setParameter('locale', $this->local, Locale::TYPE);
		
		$qb->where('category.id = :category');
		$qb->setParameter('category', $category, CategoryUid::TYPE);
		
		//dd($result);
		
		return $qb->getQuery()->getResult();
		
		//        $this->offers = new ArrayCollection();
		//        $this->trans = new ArrayCollection();
		//
		//        foreach($result as $offer)
		//        {
		//            if($offer instanceof Category\Offers)
		//            {
		//                $this->offers->add($offer);
		//            }
		//
		//            if($offer instanceof Category\Offers\Trans)
		//            {
		//                $key = (string) $offer->getOffer()->getId();
		//                $this->trans->set($key, $offer->getName());
		//            }
		//        }
		//
		//        return $this;
	}
	
	/**
	 * @return ArrayCollection
	 */
	public function getOffers() : ArrayCollection
	{
		return $this->offers;
	}
	
	/**
	 * @param string $key
	 *
	 * @return string|null
	 */
	public function getTrans(string $key) : ?string
	{
		return $this->trans->get($key);
	}
	
}