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

namespace BaksDev\Products\Category\Repository\AllCategory;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Core\Form\Search\SearchDTO;
use BaksDev\Core\Services\Paginator\PaginatorInterface;
use BaksDev\Core\Services\Switcher\SwitcherInterface;
use BaksDev\Core\Type\Locale\Locale;
use BaksDev\Products\Category\Entity as EntityCategory;
use BaksDev\Products\Category\Type\Parent\ProductParentCategoryUid;
use Symfony\Contracts\Translation\TranslatorInterface;

final class AllCategoryRepository implements AllCategoryInterface
{
    private TranslatorInterface $translator;
    private SwitcherInterface $switcher;
    private PaginatorInterface $paginator;
    private DBALQueryBuilder $DBALQueryBuilder;

    public function __construct(
        DBALQueryBuilder $DBALQueryBuilder,
        TranslatorInterface $translator,
        SwitcherInterface $switcher,
        PaginatorInterface $paginator
    )
    {

        $this->translator = $translator;
        $this->switcher = $switcher;
        $this->paginator = $paginator;
        $this->DBALQueryBuilder = $DBALQueryBuilder;
    }

    /** Возвращает список категорий с ключами:.
     *
     * id - идентификатор <br>
     * event - идентификатор события <br>
     * category_sort - сортирвка <br>
     * category_parent - идентификатор родителя категории <br>
     * category_cover_name - название файла обложки  <br>
     * category_cover_ext - расширение  файла обложки <br>
     * category_cover_cdn - флаг загрузки файла CDN <br>
     * category_cover_dir - директория  файла обложки <br>
     * category_name - название катеогрии <br>
     * category_description - краткое описание обложки <br>
     * category_child_count - количество вложенных категорий <br>
     */
    public function fetchProductParentAllAssociative(
        SearchDTO $search = null,
        ?ProductParentCategoryUid $parent = null
    ): PaginatorInterface
    {
        $local = new Locale($this->translator->getLocale());

        $qb = $this->DBALQueryBuilder->createQueryBuilder(self::class);

        // Категория
        $qb->select('category.id');
        // $qb->addSelect('category.id');
        $qb->addSelect('category.event'); // ID события
        $qb->from(EntityCategory\ProductCategory::TABLE, 'category');

        // События категории
        $qb->addSelect('category_event.sort AS category_sort');
        $qb->addSelect('category_event.parent AS category_parent');

        $qb->join(
            'category',
            EntityCategory\Event\ProductCategoryEvent::TABLE,
            'category_event',
            'category_event.id = category.event AND '.
            ($parent ? 'category_event.parent = :parent_category' : 'category_event.parent IS NULL')
        );

        // Обложка
        $qb->addSelect('category_cover.name AS category_cover_name');
        $qb->addSelect('category_cover.ext AS category_cover_ext');
        $qb->addSelect('category_cover.cdn AS category_cover_cdn');

        $qb->leftJoin(
            'category_event',
            EntityCategory\Cover\ProductCategoryCover::TABLE,
            'category_cover',
            'category_cover.event = category_event.id'
        );

        if($parent)
        {
            $qb->setParameter('parent_category', $parent, ProductParentCategoryUid::TYPE);
        }

        // Перевод категории
        $qb->addSelect('category_trans.name AS category_name');
        $qb->addSelect('category_trans.description AS category_description');

        $qb->leftJoin(
            'category_event',
            EntityCategory\Trans\ProductCategoryTrans::TABLE,
            'category_trans',
            'category_trans.event = category_event.id AND category_trans.local = :local'
        );

        $qb->setParameter('local', $local, Locale::TYPE);

        /** Количество вложенных категорий */

        // EXISTS Event IN Category
        $qbCounterExist = $this->DBALQueryBuilder->builder();
        $qbCounterExist->select('1');
        $qbCounterExist->from(EntityCategory\ProductCategory::TABLE, 'count_cat');
        $qbCounterExist->where('count_cat.id = category_event_count.category');
        $qbCounterExist->andWhere('count_cat.event = category_event_count.id');

        // COUNT Event
        $qbCounter = $this->DBALQueryBuilder->builder();
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

        $qb->addSelect('('.$qbCounter->getSQL().') AS category_child_count');

        // END Количество вложенных категорий

        // Поиск
        if($search?->query)
        {
            // $Switcher = new Switcher();

            $qb->andWhere(
                '
                        LOWER(category_trans.name) LIKE :query OR
                        LOWER(category_trans.name) LIKE :switcher OR
                        
                        /*LOWER(parent_category_trans.name) LIKE :query OR
                        LOWER(parent_category_trans.name) LIKE :switcher OR*/

                        LOWER(category_trans.description) LIKE :query OR
                        LOWER(category_trans.description) LIKE :switcher

                    '
            );

            $qb->setParameter('query', '%'.$this->switcher->toRus($search->query, true).'%');
            $qb->setParameter('switcher', '%'.$this->switcher->toEng($search->query, true).'%');
        }

        $qb->orderBy('category_event.sort', 'ASC');


        return $this->paginator->fetchAllAssociative($qb);

    }

    /** TODO рекурсивный запрос */

    /**
     * @param string[] $cteQueries
     */
    public function buildSql(string $mainSql, array $cteQueries, bool $recursive = false): string
    {
        if(empty($cteQueries))
        {
            return $mainSql;
        }

        $ctes = [];

        foreach($cteQueries as $alias => $sqlQuery)
        {
            $ctes[] = sprintf('%s AS (%s)', $alias, $sqlQuery);
        }

        if($recursive)
        {
            return sprintf("WITH RECURSIVE %s\n%s", implode(",\n", $ctes), $mainSql);
        }

        return sprintf("WITH %s\n%s", implode(",\n", $ctes), $mainSql);
    }
}
