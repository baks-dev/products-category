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

namespace BaksDev\Products\Category\Repository\AllCategory;

use BaksDev\Products\Category\Entity as EntityCategory;
use BaksDev\Products\Category\Type\Parent\ParentCategoryUid;
use BaksDev\Core\Form\Search\SearchDTO;
use BaksDev\Core\Services\Switcher\SwitcherInterface;
use BaksDev\Core\Type\Locale\Locale;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Symfony\Contracts\Translation\TranslatorInterface;

final class AllCategoryQuery implements AllCategoryInterface
{
    private Connection $connection;
    private TranslatorInterface $translator;
    private SwitcherInterface $switcher;
    
    public function __construct(Connection $connection, TranslatorInterface $translator, SwitcherInterface $switcher)
    {
        $this->connection = $connection;
        $this->translator = $translator;
        $this->switcher = $switcher;
    }
    
    /**
     * @param string   $mainSql
     * @param string[] $cteQueries
     * @param bool     $recursive
     *
     * @return string
     */
    public function buildSql(string $mainSql, array $cteQueries, bool $recursive = false): string
    {
        if (empty($cteQueries)) {
            return $mainSql;
        }
        
        $ctes = array();
        
        foreach ($cteQueries as $alias => $sqlQuery) {
            $ctes[] = sprintf('%s AS (%s)', $alias, $sqlQuery);
        }
        
        if ($recursive) {
            return sprintf("WITH RECURSIVE %s\n%s", implode(",\n", $ctes), $mainSql);
        }
        
        return sprintf("WITH %s\n%s", implode(",\n", $ctes), $mainSql);
    }
    
    
    
    public function get(SearchDTO $search, ?ParentCategoryUid $parent) : QueryBuilder
    {
        $local = new Locale($this->translator->getLocale());

        $qb = $this->connection->createQueryBuilder();
        
        
    
        /** Категория */
        $qb->select('category.id');
        $qb->addSelect('category.id');
        $qb->addSelect('category.event'); /* ID события */
        $qb->from(EntityCategory\ProductCategory::TABLE, 'category');
    
    
       
    
        /** События категории */
        $qb->addSelect('category_event.sort');
        $qb->addSelect('category_event.parent');
    
        $qb->join
        (
          'category',
          EntityCategory\Event\ProductCategoryEvent::TABLE,
          'category_event',
          'category_event.id = category.event AND '.
          ($parent ? 'category_event.parent = :parent_category' : 'category_event.parent IS NULL')
        );
    
    
       
        
        /** Обложка */
        $qb->addSelect('category_cover.name AS cover');
        $qb->addSelect('category_cover.ext');
        $qb->addSelect('category_cover.cdn');
        $qb->addSelect('category_cover.dir');
        $qb->leftJoin(
          'category_event',
          EntityCategory\Cover\ProductCategoryCover::TABLE,
          'category_cover',
          'category_cover.event_id = category_event.id');
        
    
        if($parent)
        {
            $qb->setParameter('parent_category', $parent, ParentCategoryUid::TYPE);
        }
    
        /** Перевод категории */
        $qb->addSelect('category_trans.name');
        $qb->addSelect('category_trans.description');
    
        $qb->leftJoin(
          'category_event',
          EntityCategory\Trans\ProductCategoryTrans::TABLE,
          'category_trans',
          'category_trans.event_id = category_event.id AND category_trans.local = :local');
    
        $qb->setParameter('local', $local, Locale::TYPE);
    

    
        /** Количество вложенных категорий */
        
        /* EXISTS Event IN Category */
        $qbCounterExist = $this->connection->createQueryBuilder();
        $qbCounterExist->select('1');
        $qbCounterExist->from(EntityCategory\ProductCategorySettings::TABLE, 'count_cat');
        $qbCounterExist->where('count_cat.id = category_event_count.category');
        $qbCounterExist->andWhere('count_cat.event = category_event_count.id');
    
        /* COUNT Event */
        $qbCounter = $this->connection->createQueryBuilder();
        $qbCounter->select('COUNT(category_event_count.id)');
        $qbCounter->from(EntityCategory\Event\ProductCategoryEvent::TABLE, 'category_event_count');
        
        $qbCounter->join(
          'category_event_count',
          EntityCategory\ProductCategory::TABLE,
          'count_cat',
          'count_cat.id = category_event_count.category AND count_cat.event = category_event_count.id'
        );
    
        $qbCounter->where('category_event_count.parent = category.id');
        $qbCounter->andWhere('EXISTS ('.$qbCounterExist->getSQL().')');
    
        $qb->addSelect('('.$qbCounter->getSQL().') AS counter');
        /** END Количество вложенных категорий */
    

        /* Поиск */
        if($search->query)
        {
            //$Switcher = new Switcher();
        
            $qb->andWhere(
              '
                        LOWER(category_trans.name) LIKE :query OR
                        LOWER(category_trans.name) LIKE :switcher OR
                        
                        /*LOWER(parent_category_trans.name) LIKE :query OR
                        LOWER(parent_category_trans.name) LIKE :switcher OR*/

                        LOWER(category_trans.description) LIKE :query OR
                        LOWER(category_trans.description) LIKE :switcher

                    ');
        
            $qb->setParameter('query', '%'.$this->switcher->toRus($search->query, true).'%');
            $qb->setParameter('switcher', '%'.$this->switcher->toEng($search->query, true).'%');
        
        }
    
        $qb->orderBy('category_event.sort', 'ASC');
    
    
        
        
        
        return $qb;
        
    }
    
    
    
    
    
    
    
    

    
    
    
    
    
    
}