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

namespace BaksDev\Products\Category\Repository\OfferFieldsByCategoryChoiceForm;


use BaksDev\Products\Category\Entity;
use BaksDev\Products\Category\Type\Id\CategoryUid;
use BaksDev\Products\Category\Type\Offers\Id\OffersUid;
use BaksDev\Core\Type\Locale\Locale;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class OfferFieldsByCategoryChoiceRepository implements OfferFieldsByCategoryChoiceInterface
{
    //    private ArrayCollection $delivery;
    //    private ArrayCollection $trans;
    //    private ArrayCollection $price;
    //    private ArrayCollection $currency;
    //
    //    private TokenStorageInterface $token;
    //    private ArrayCollection $type;
    
    private ArrayCollection $offers;
    private ArrayCollection $trans;
    
    private TranslatorInterface $translator;
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
    
    public function get(CategoryUid $category) : array
    {

        $qb = $this->entityManager->createQueryBuilder();
        $select = sprintf('offers.id,
        new %s(
            offers.id,
            offers_trans.name,
            offers.multiple
        )',
                          OffersUid::class);
        
        $qb->select($select);

        $qb->from(Entity\Offers\Offers::class, 'offers', 'offers.id');
        $qb->join(Entity\Category::class, 'category', 'WITH', 'category.event = offers.event');
        
        $qb->join(
          Entity\Offers\Trans\Trans::class,
          'offers_trans',
          'WITH',
          'offers_trans.offer = offers.id AND offers_trans.local = :locale');
        
        $qb->setParameter('locale', $this->local, Locale::TYPE);
        
        $qb->where('category.id = :category');
        $qb->setParameter('category', $category, CategoryUid::TYPE);
        
        
        return array_map(function($array) {  return end($array);  }, $qb->getQuery()->getResult());
        
    }
    
}