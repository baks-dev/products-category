<?php
/*
 *  Copyright 2024.  Baks.dev <admin@baks.dev>
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

declare(strict_types=1);

namespace BaksDev\Products\Category\Repository\CategoryProductCurrentEvent;

use BaksDev\Core\Doctrine\ORMQueryBuilder;
use BaksDev\Products\Category\Entity\CategoryProduct;
use BaksDev\Products\Category\Entity\Event\CategoryProductEvent;
use BaksDev\Products\Category\Type\Event\CategoryProductEventUid;
use BaksDev\Products\Category\Type\Id\CategoryProductUid;
use InvalidArgumentException;

final class CategoryProductCurrentEventRepository implements CategoryProductCurrentEventInterface
{
    private CategoryProductEventUid|false $event = false;

    private CategoryProductUid|false $main;

    public function __construct(private readonly ORMQueryBuilder $ORMQueryBuilder) {}

    public function forEvent(CategoryProductEvent|CategoryProductEventUid|string $event): self
    {
        if(is_string($event))
        {
            $event = new CategoryProductEventUid($event);
        }

        if($event instanceof CategoryProductEvent)
        {
            $event = $event->getId();
        }

        $this->event = $event;

        return $this;
    }


    public function forMain(CategoryProduct|CategoryProductUid|string $main): self
    {
        if(is_string($main))
        {
            $main = new CategoryProductUid($main);
        }

        if($main instanceof CategoryProduct)
        {
            $main = $main->getId();
        }

        $this->main = $main;

        return $this;
    }

    /** Метод возвращает активное событие категории продукции */
    public function find(): CategoryProductEvent|false
    {
        if($this->event === false && $this->main === false)
        {
            throw new InvalidArgumentException('Invalid Argument CategoryProductEventUid or CategoryProductUid');
        }

        if($this->event !== false && $this->main !== false)
        {
            throw new InvalidArgumentException('Вызов двух аргументов CategoryProductEventUid и CategoryProductUid');
        }

        $qb = $this->ORMQueryBuilder->createQueryBuilder(self::class);

        if($this->main !== false)
        {
            $qb
                ->from(CategoryProduct::class, 'main')
                ->where('main.id = :main')
                ->setParameter('main', $this->main, CategoryProductUid::TYPE);
        }

        if($this->event !== false)
        {
            $qb
                ->from(CategoryProductEvent::class, 'event')
                ->where('event.id = :event')
                ->setParameter('event', $this->event, CategoryProductEventUid::TYPE);

            $qb->join(
                CategoryProduct::class,
                'main',
                'WITH',
                'main.id = event.category'
            );
        }


        $qb
            ->select('current')
            ->leftJoin(
                CategoryProductEvent::class,
                'current',
                'WITH',
                'current.id = main.event'
            );

        return $qb->getQuery()->getOneOrNullResult() ?: false;
    }
}
