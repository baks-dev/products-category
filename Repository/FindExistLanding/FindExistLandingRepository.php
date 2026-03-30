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

namespace BaksDev\Products\Category\Repository\FindExistLanding;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Products\Category\Entity\Landing\CategoryProductLanding;
use BaksDev\Products\Category\Type\Event\CategoryProductEventUid;
use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;
use InvalidArgumentException;

final class FindExistLandingRepository implements FindExistLandingInterface
{

    private UserProfileUid|bool $profile = false;

    private CategoryProductEventUid|bool $event = false;

    public function __construct(private readonly DBALQueryBuilder $DBALQueryBuilder) {}

    public function byProfile(UserProfileUid $profile): self
    {
        $this->profile = $profile;
        return $this;
    }

    public function byEvent(CategoryProductEventUid $event): self
    {
        $this->event = $event;
        return $this;
    }

    public function exist(): bool
    {
        if($this->profile === false)
        {
            throw new InvalidArgumentException(
                sprintf('Не задан параметр profile (%s)', self::class.':'.__LINE__)
            );
        }

        if($this->event === false)
        {
            throw new InvalidArgumentException(
                sprintf('Не задан параметр event (%s)', self::class.':'.__LINE__)
            );
        }

        $dbal = $this->DBALQueryBuilder->createQueryBuilder(self::class);

        $dbal
            ->from(CategoryProductLanding::class, 'product_category_landing')
            ->where(' product_category_landing.profile = :profile')
            ->andWhere(' product_category_landing.event = :event')
            ->setParameter(
                key: 'event',
                value: $this->event,
                type: CategoryProductEventUid::TYPE,
            )
            ->setParameter(
                key: 'profile',
                value: $this->profile,
                type: UserProfileUid::TYPE,
            );

        return $dbal->fetchExist();
    }


    public function existByEvent(): bool
    {

        if($this->event === false)
        {
            throw new InvalidArgumentException(
                sprintf('Не задан параметр event  (%s)', self::class.':'.__LINE__)
            );
        }

        $dbal = $this->DBALQueryBuilder->createQueryBuilder(self::class);

        $dbal
            ->from(CategoryProductLanding::class, 'product_category_landing')
            ->where(' product_category_landing.event = :event')
            ->setParameter(
                key: 'event',
                value: $this->event,
                type: CategoryProductEventUid::TYPE,
            );

        return $dbal->fetchExist();
    }

}