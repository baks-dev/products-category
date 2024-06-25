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

declare(strict_types=1);

namespace BaksDev\Products\Category\Repository\UniqCategoryUrl;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Products\Category\Entity\Event\CategoryProductEvent;
use BaksDev\Products\Category\Entity\Info\CategoryProductInfo;
use BaksDev\Products\Category\Type\Event\CategoryProductEventUid;

final class UniqCategoryUrlRepository implements UniqCategoryUrlInterface
{
    private CategoryProductEventUid|string|CategoryProductEvent $event;

    public function __construct(private readonly DBALQueryBuilder $DBALQueryBuilder) {}

    public function excludeEvent(CategoryProductEventUid|CategoryProductEvent|string $event): self
    {
        if($event instanceof CategoryProductEvent)
        {
            $event = $event->getId();
        }

        if(is_string($event))
        {
            $event = new CategoryProductEventUid($event);
        }

        $this->event = $event;
        return $this;
    }

    public function isExistUrl(string $url): bool
    {
        $dbal = $this->DBALQueryBuilder->createQueryBuilder(self::class);

        $dbal
            ->from(CategoryProductInfo::class, 'info')
            ->where('info.url = :url')
            ->setParameter('url', $url);

        /** Исключить событие ( Exclude Event )*/
        if($this->event)
        {
            $dbal
                ->andWhere('info.event != :event')
                ->setParameter('event', $this->event, CategoryProductEventUid::TYPE);
        }

        return $dbal->fetchExist();
    }
}
