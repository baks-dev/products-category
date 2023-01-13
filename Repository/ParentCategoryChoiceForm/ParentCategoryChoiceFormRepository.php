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

namespace App\Module\Products\Category\Repository\ParentCategoryChoiceForm;


use App\Module\Products\Category\Entity as EntityCategory;
use App\Module\Products\Category\Type\Id\CategoryUid;
use App\Module\Products\Category\Type\Parent\ParentCategoryUid;
use App\System\Type\Locale\Locale;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/** Клас получает массив категорий для формы и перобразует названия (path) согласно вложенности */
final class ParentCategoryChoiceFormRepository implements ParentCategoryChoiceFormInterface
{

    private Locale $local;
    private EntityManagerInterface $entityManager;
    
    public function __construct(EntityManagerInterface $entityManager, TranslatorInterface $translator) {
        $this->entityManager = $entityManager;
        $this->local = new Locale($translator->getLocale());
    }
    
    public function get(?CategoryUid $categoryUid = null) : array
    {

        $qb = $this->entityManager->createQueryBuilder();
        
        $select = sprintf('new %s(category.id, trans.name)', ParentCategoryUid::class);
        

        $qb->select($select);
    
//        $qb->select('category.id');
//
//        $qb->addSelect('category.event');
//        $qb->addSelect('event.parent');
//
//        $qb->addSelect('trans.name');

        $qb->from(EntityCategory\Category::class, 'category', 'category.id');
        $qb->join(EntityCategory\Event\Event::class, 'event', 'WITH', 'event.id = category.event AND event.category = category.id');
        $qb->leftJoin(EntityCategory\Trans\Trans::class, 'trans', 'WITH', 'trans.event = event.id AND trans.local = :locale');

        $qb->setParameter('locale', $this->local, Locale::TYPE);
        
        $qb->orderBy(
          'CASE
        WHEN
            event.parent IS NULL
        THEN
            category.id
        ELSE
            event.parent
        END',
          'DESC');
        
        

        
        return $qb->getQuery()->getResult();
        

       $result = $qb->getQuery()->getResult();

        /* Преобразуем названия (path) разделов с учетом вложенных */
        $CategoryParent = [];

        foreach($result as $key => $item)
        {

            $choice_label = $item['name'];
            
            $parentCat = (string) $item['parent']->getValue();
            
            if(!empty($parentCat))
            {
                $choice_label = $result[$parentCat]['name'].' / '.$choice_label;
            }

            $CategoryParent[$choice_label] = $key;
        }

        return $CategoryParent;
    }
    
}