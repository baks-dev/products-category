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

namespace App\Module\Products\Category\Repository\CategoryChoice;


use App\Module\Products\Category\Entity;
use App\Module\Products\Category\Type\Id\CategoryUid;
use App\System\Type\Locale\Locale;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Translation\TranslatorInterface;

final class CategoryChoice implements CategoryChoiceInterface
{
    private Locale $local;
    private EntityManagerInterface $entityManager;
    private FilesystemAdapter $cacheQueries;
    
    public function __construct(EntityManagerInterface $entityManager, TranslatorInterface $translator) {
        $this->entityManager = $entityManager;
        $this->local = new Locale($translator->getLocale());
    
        $this->cacheQueries = new FilesystemAdapter('ProductCategory');
    }
    
    public function get()
    {
        $qb = $this->entityManager->createQueryBuilder();
        
        $select = sprintf('new %s(category.id, trans.name)', CategoryUid::class);
    
        $qb->select($select);
    
        $qb->from(Entity\Category::class, 'category', 'category.id');
        $qb->join(Entity\Event\Event::class, 'event', 'WITH', 'event.id = category.event AND event.category = category.id');
        $qb->leftJoin(Entity\Trans\Trans::class, 'trans', 'WITH', 'trans.event = event.id AND trans.local = :local');

    
        /* Кеширование */
        $query = $this->entityManager->createQuery($qb->getDQL());
        $query->setQueryCache($this->cacheQueries);
        $query->setResultCache($this->cacheQueries);
        $query->enableResultCache();
    
        $query->setParameter('local', $this->local, Locale::TYPE);
    
        return $query->getResult();
        
        
        
        
    }
    
}