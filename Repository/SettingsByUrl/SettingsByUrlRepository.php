<?php
/*
 *  Copyright 2026.  Baks.dev <admin@baks.dev>
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

namespace BaksDev\Products\Category\Repository\SettingsByUrl;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Products\Category\Entity\Info\CategoryProductInfo;
use BaksDev\Products\Category\Entity\Offers\CategoryProductOffers;
use BaksDev\Products\Category\Entity\Offers\Variation\CategoryProductVariation;
use BaksDev\Products\Category\Entity\Offers\Variation\Modification\CategoryProductModification;

final class SettingsByUrlRepository implements SettingsByUrlInterface
{
    public function __construct(private DBALQueryBuilder $DBALQueryBuilder) {}

    /**
     *  Категория по символьному коду url
     */
    public function find(string $url): SettingsByUrlResult|false
    {

        $dbal = $this
            ->DBALQueryBuilder->createQueryBuilder(self::class)
            ->bindLocal();

        $dbal
            ->from(CategoryProductInfo::class, 'info')
            ->where('info.url = :url')
            ->andWhere('info.active IS TRUE')
            ->setParameter('url', $url);

        $dbal
            ->addSelect('offer.id AS offer')
            ->leftJoin(
                'info',
                CategoryProductOffers::class,
                'offer',
                'offer.event = info.event',
            );

        $dbal
            ->addSelect('variation.id AS variation')
            ->leftJoin(
                'offer',
                CategoryProductVariation::class,
                'variation',
                'variation.offer = offer.id',
            );

        $dbal
            ->addSelect('modification.id AS modification')
            ->leftJoin(
                'variation',
                CategoryProductModification::class,
                'modification',
                'modification.variation = variation.id',
            );

        return $dbal->fetchHydrate(SettingsByUrlResult::class);
    }
}