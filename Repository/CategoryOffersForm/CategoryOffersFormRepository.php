<?php
/*
 *  Copyright 2022.  Baks.dev <admin@baks.dev>
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *  http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *   limitations under the License.
 *
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
        $select = sprintf('offers, new %s(
            offers.id,
            offers.reference,
            offers.image,
            offers.price,
            offers.quantitative,
            offers.article,
            offers.multiple,
            offers_trans.name
        )',
          CategoryOffersFormDTO::class);
        
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
          'offers_trans.offer = offers.id AND offers_trans.local = :locale');
        
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
     * @return string|null
     */
    public function getTrans(string $key) : ?string
    {
        return $this->trans->get($key);
    }
    
}