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

namespace BaksDev\Products\Category\UseCase\Admin\Delete\Tests;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Products\Category\Entity\CategoryProduct;
use BaksDev\Products\Category\Entity\Event\CategoryProductEvent;
use BaksDev\Products\Category\Repository\CategoryProductCurrentEvent\CategoryProductCurrentEventInterface;
use BaksDev\Products\Category\Type\Id\CategoryProductUid;
use BaksDev\Products\Category\UseCase\Admin\Delete\DeleteCategoryProductDTO;
use BaksDev\Products\Category\UseCase\Admin\Delete\DeleteProductCategoryHandler;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\CategoryProductHandler;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\Tests\CategoryProductEditTest;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\Tests\CategoryProductNewTest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;

/**
 * @group category-product
 * @group category-product-usecase
 *
 * @depends BaksDev\Products\Category\UseCase\Admin\NewEdit\Tests\CategoryProductNewTest::class
 * @depends BaksDev\Products\Category\UseCase\Admin\NewEdit\Tests\CategoryProductEditTest::class
 */
#[When(env: 'test')]
class CategoryProductDeleteTest extends KernelTestCase
{
    public static function tearDownAfterClass(): void
    {
        /** @var EntityManagerInterface $em */
        $em = self::getContainer()->get(EntityManagerInterface::class);

        $main = $em->getRepository(CategoryProduct::class)
            ->findOneBy(['id' => CategoryProductUid::TEST]);

        if($main)
        {
            $em->remove($main);
        }


        $event = $em->getRepository(CategoryProductEvent::class)
            ->findBy(['category' => CategoryProductUid::TEST]);

        foreach($event as $remove)
        {
            $em->remove($remove);
        }

        $em->flush();
        $em->clear();
    }

    public function testUseCase(): void
    {
        /** @var CategoryProductCurrentEventInterface $CategoryProductCurrentEvent */
        $CategoryProductCurrentEvent = self::getContainer()->get(CategoryProductCurrentEventInterface::class);
        $CategoryProductEvent = $CategoryProductCurrentEvent->forMain(CategoryProductUid::TEST)->find();
        self::assertNotNull($CategoryProductEvent);


        /** @see CategoryProductDeleteDTO */
        $CategoryProductDeleteDTO = new DeleteCategoryProductDTO();
        $CategoryProductEvent->getDto($CategoryProductDeleteDTO);


        /** @var CategoryProductHandler $CategoryProductHandler */
        $CategoryProductDeleteHandler = self::getContainer()->get(DeleteProductCategoryHandler::class);
        $handle = $CategoryProductDeleteHandler->handle($CategoryProductDeleteDTO);

        self::assertTrue(($handle instanceof CategoryProduct), $handle.': Ошибка CategoryProduct');

    }

    public function testComplete(): void
    {
        /** @var DBALQueryBuilder $dbal */
        $dbal = self::getContainer()->get(DBALQueryBuilder::class);

        $dbal->createQueryBuilder(self::class);

        $dbal->from(CategoryProduct::class)
            ->where('id = :id')
            ->setParameter('id', CategoryProductUid::TEST);

        self::assertFalse($dbal->fetchExist());

    }
}
