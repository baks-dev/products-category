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

namespace BaksDev\Products\Category\Repository\CategoryChoice;

use BaksDev\Core\Doctrine\ORMQueryBuilder;
use BaksDev\Core\Type\Locale\Locale;
use BaksDev\Products\Category\Entity;
use BaksDev\Products\Category\Type\Id\ProductCategoryUid;
use Symfony\Contracts\Translation\TranslatorInterface;

final class CategoryChoiceRepository implements CategoryChoiceInterface
{
    private TranslatorInterface $translator;
    private ORMQueryBuilder $ORMQueryBuilder;


    public function __construct(
        ORMQueryBuilder $ORMQueryBuilder,
        TranslatorInterface $translator,
    )
    {
        $this->translator = $translator;
        $this->ORMQueryBuilder = $ORMQueryBuilder;
    }

    /** Метод возвращает коллекцию категорий продукции */
    public function getCategoryCollection(?ProductCategoryUid $category = null)
    {
        $qb = $this->ORMQueryBuilder->createQueryBuilder(self::class)->bindLocal();

        $select = sprintf('new %s(category.id, trans.name)', ProductCategoryUid::class);

        $qb->select($select);

        $qb->from(Entity\ProductCategory::class, 'category', 'category.id');

        /* Выбираем только активные */
        $qb->join(
            Entity\Info\ProductCategoryInfo::class,
            'info',
            'WITH',
            'info.event = category.event AND info.active = true',
        );

        $qb->join(
            Entity\Event\ProductCategoryEvent::class,
            'event',
            'WITH',
            'event.id = category.event AND event.category = category.id',
        );
        $qb->leftJoin(
            Entity\Trans\ProductCategoryTrans::class,
            'trans',
            'WITH',
            'trans.event = event.id AND trans.local = :local',
        );

        /* Кешируем результат ORM */
        return $qb->enableCache('products-category', 86400)->getResult();
    }


    /**
     * Метод возвращает идентификатор категории с названием в аттрибуте
     */
    public function getProductCategory(ProductCategoryUid $category) : ?ProductCategoryUid
    {
        $qb = $this->ORMQueryBuilder->createQueryBuilder(self::class)->bindLocal();

        $select = sprintf('new %s(category.id, trans.name)', ProductCategoryUid::class);

        $qb->select($select);

        $qb
            ->from(Entity\ProductCategory::class, 'category', 'category.id')
            ->where('category.id = :category')
            ->setParameter('category', $category, ProductCategoryUid::TYPE)
        ;

        /* Выбираем только активные */
        $qb->join(
            Entity\Info\ProductCategoryInfo::class,
            'info',
            'WITH',
            'info.event = category.event AND info.active = true',
        );

        $qb->join(
            Entity\Event\ProductCategoryEvent::class,
            'event',
            'WITH',
            'event.id = category.event AND event.category = category.id',
        );
        $qb->leftJoin(
            Entity\Trans\ProductCategoryTrans::class,
            'trans',
            'WITH',
            'trans.event = event.id AND trans.local = :local',
        );


        /* Кешируем результат ORM */
        return $qb->enableCache('products-category', 86400)->getOneOrNullResult();
    }
}