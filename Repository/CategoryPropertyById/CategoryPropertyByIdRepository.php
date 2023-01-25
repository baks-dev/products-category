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

namespace BaksDev\Products\Category\Repository\CategoryPropertyById;


use BaksDev\Products\Category\Entity;
use BaksDev\Products\Category\Type\Id\CategoryUid;
use BaksDev\Core\Type\Locale\Locale;
//use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
//use Doctrine\ORM\AbstractQuery;
//use Doctrine\ORM\Cache\DefaultEntityHydrator;
use Doctrine\ORM\EntityManagerInterface;
//use Doctrine\ORM\Internal\Hydration\ObjectHydrator;
//use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\Translation\TranslatorInterface;

final class CategoryPropertyByIdRepository implements CategoryPropertyByIdInterface
{
    private Locale $local;
    private EntityManagerInterface $entityManager;
    
    public function __construct(EntityManagerInterface $entityManager, TranslatorInterface $translator)
    {
        $this->local = new Locale($translator->getLocale());
        $this->entityManager = $entityManager;
    }
    
    public function get(CategoryUid $categoryUid) : array
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
          CategoryPropertyDTO::class);
        
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
          'section_trans.section = section.id AND section_trans.local = :locale');
        

        /* Перевод полей */
        //$qb->addSelect('field.id');
        $qb->join(Entity\Section\Field\Field::class,
                  'field',
                  'WITH',
                  'field.section = section.id'
        );
        
        
        $qb->join(
          Entity\Section\Field\Trans\Trans::class,
          'field_trans',
          'WITH',
          'field_trans.field = field.id AND field_trans.local = :locale');
        
        $qb->setParameter('locale', $this->local, Locale::TYPE);
        
        $qb->where('category.id = :category');
        $qb->setParameter('category', $categoryUid, CategoryUid::TYPE);
        
        $qb->orderBy('section.sort', 'ASC');
        $qb->addOrderBy('field.sort', 'ASC');

       
        
        /* Преобразуем ключи */
        $dto = null;
        foreach($qb->getQuery()->getResult() as $item)
        {
            $dto[(string)$item->fieldUid] = $item;
        }

        return $dto;
    }
    
}