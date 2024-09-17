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

namespace BaksDev\Products\Category\UseCase\Admin\NewEdit\Tests;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Core\Type\Field\InputField;
use BaksDev\Products\Category\Repository\CategoryProductCurrentEvent\CategoryProductCurrentEventInterface;
use BaksDev\Products\Category\Type\Id\CategoryProductUid;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\CategoryProductDTO;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\Landing\CategoryProductLandingCollectionDTO;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\Offers\CategoryProductOffersDTO;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\Offers\Trans\CategoryProductOffersTransDTO;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\Offers\Variation\CategoryProductVariationDTO;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\Offers\Variation\Modification\CategoryProductModificationDTO;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\Offers\Variation\Modification\Trans\CategoryProductModificationTransDTO;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\Offers\Variation\Trans\CategoryProductVariationTransDTO;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\Section\CategoryProductSectionCollectionDTO;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\Section\Fields\CategoryProductSectionFieldCollectionDTO;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\Section\Fields\Trans\CategoryProductSectionFieldTransDTO;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\Section\Trans\CategoryProductSectionTransDTO;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\Seo\CategoryProductSeoCollectionDTO;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\Trans\CategoryProductTransDTO;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;

/**
 * @group category-product
 * @group category-product-usecase
 *
 * @depends BaksDev\Products\Category\UseCase\Admin\NewEdit\Tests\CategoryProductNewTest::class
 */
#[When(env: 'test')]
class CategoryProductEditTest extends KernelTestCase
{
    public function testUseCase(): void
    {
        /** @var CategoryProductCurrentEventInterface $CategoryProductCurrentEvent */
        $CategoryProductCurrentEvent = self::getContainer()->get(CategoryProductCurrentEventInterface::class);
        $CategoryProductEvent = $CategoryProductCurrentEvent
            ->forMain(CategoryProductUid::TEST)
            ->find();


        self::assertNotNull($CategoryProductEvent);
        self::assertNotFalse($CategoryProductEvent);

        /** @see CategoryProductDTO */
        $CategoryProductDTO = new CategoryProductDTO();
        $CategoryProductEvent->getDto($CategoryProductDTO);

        self::assertEquals(123, $CategoryProductDTO->getSort());
        $CategoryProductDTO->setSort(321);

        $ProductInfoDTO = $CategoryProductDTO->getInfo();


        self::assertFalse($ProductInfoDTO->getActive());
        $ProductInfoDTO->setActive(true);


        self::assertEquals('test_category_url', $ProductInfoDTO->getUrl());
        $ProductInfoDTO->setUrl('edit_test_category_url');


        $CategoryProductDTO->getLanding();

        /** @var CategoryProductLandingCollectionDTO $ProductLandingCollectionDTO */
        foreach($CategoryProductDTO->getLanding() as $ProductLandingCollectionDTO)
        {
            self::assertEquals('Test Landing Header', $ProductLandingCollectionDTO->getHeader());
            $ProductLandingCollectionDTO->setHeader('Edit Test Landing Header');

            self::assertEquals('Test Landing Bottom', $ProductLandingCollectionDTO->getBottom());
            $ProductLandingCollectionDTO->setBottom('Edit Test Landing Bottom');

        }


        /** @var CategoryProductSeoCollectionDTO $ProductSeoCollectionDTO */
        foreach($CategoryProductDTO->getSeo() as $ProductSeoCollectionDTO)
        {
            self::assertEquals('Test Category Seo Title', $ProductSeoCollectionDTO->getTitle());
            $ProductSeoCollectionDTO->setTitle('Edit Test Category Seo Title');

            self::assertEquals('Test Category Seo Description', $ProductSeoCollectionDTO->getDescription());
            $ProductSeoCollectionDTO->setDescription('Edit Test Category Seo Description');

            self::assertEquals('Test Category Seo Keywords', $ProductSeoCollectionDTO->getKeywords());
            $ProductSeoCollectionDTO->setKeywords('Edit Test Category Seo Keywords');

        }


        /** @var CategoryProductSectionCollectionDTO $ProductSectionCollectionDTO */

        self::assertCount(1, $CategoryProductDTO->getSection());
        $ProductSectionCollectionDTO = $CategoryProductDTO->getSection()->current();

        /** @var CategoryProductSectionFieldCollectionDTO $ProductSectionFieldCollectionDTO */

        self::assertCount(1, $ProductSectionCollectionDTO->getField());
        $ProductSectionFieldCollectionDTO = $ProductSectionCollectionDTO->getField()->current();

        self::assertEquals(112, $ProductSectionFieldCollectionDTO->getSort());
        $ProductSectionFieldCollectionDTO->setSort(211);

        self::assertTrue($ProductSectionFieldCollectionDTO->getType()->getType() === 'input');


        self::assertFalse($ProductSectionFieldCollectionDTO->getName());
        $ProductSectionFieldCollectionDTO->setName(true);


        self::assertFalse($ProductSectionFieldCollectionDTO->getRequired());
        $ProductSectionFieldCollectionDTO->setRequired(true);


        self::assertFalse($ProductSectionFieldCollectionDTO->getAlternative());
        $ProductSectionFieldCollectionDTO->setAlternative(true);

        self::assertFalse($ProductSectionFieldCollectionDTO->getFilter());
        $ProductSectionFieldCollectionDTO->setFilter(true);


        self::assertFalse($ProductSectionFieldCollectionDTO->getPhoto());
        $ProductSectionFieldCollectionDTO->setPhoto(true);


        self::assertFalse($ProductSectionFieldCollectionDTO->getPublic());
        $ProductSectionFieldCollectionDTO->setPublic(true);


        /** @var CategoryProductSectionFieldTransDTO $ProductSectionFieldTransDTO */
        foreach($ProductSectionFieldCollectionDTO->getTranslate() as $ProductSectionFieldTransDTO)
        {
            self::assertEquals('Test Category Section Field Name', $ProductSectionFieldTransDTO->getName());
            $ProductSectionFieldTransDTO->setName('Edit Test Category Section Field Name');

            self::assertEquals('Test Category Section Field Description', $ProductSectionFieldTransDTO->getDescription());
            $ProductSectionFieldTransDTO->setDescription('Edit Category Section Field Description');

        }


        /** @var CategoryProductSectionTransDTO $ProductSectionTransDTO */
        foreach($ProductSectionCollectionDTO->getTranslate() as $ProductSectionTransDTO)
        {
            self::assertEquals('Test Category Section Name', $ProductSectionTransDTO->getName());
            $ProductSectionTransDTO->setName('Edit Test Category Section Name');

            self::assertEquals('Test Category Section Description', $ProductSectionTransDTO->getDescription());
            $ProductSectionTransDTO->setDescription('Edit Test Category Section Description');

        }


        /** @var CategoryProductTransDTO $CategoryProductTransDTO */
        foreach($CategoryProductDTO->getTranslate() as $CategoryProductTransDTO)
        {
            self::assertEquals('Test Category Name', $CategoryProductTransDTO->getName());
            $CategoryProductTransDTO->setName('Edit Test Category Name');

            self::assertEquals('Test Category Description', $CategoryProductTransDTO->getDescription());
            $CategoryProductTransDTO->setDescription('Edit Test Category Description');

        }


        /** @var CategoryProductOffersDTO $CategoryProductOffersDTO */
        $CategoryProductOffersDTO = $CategoryProductDTO->getOffer();

        /** @var CategoryProductOffersTransDTO $ProductOffersTransDTO */
        foreach($CategoryProductOffersDTO->getTranslate() as $ProductOffersTransDTO)
        {
            self::assertEquals('Test Category Offer Name', $ProductOffersTransDTO->getName());
            $ProductOffersTransDTO->setName('Edit Test Category Offer Name');

            self::assertEquals('Test Category Offer Postfix', $ProductOffersTransDTO->getPostfix());
            $ProductOffersTransDTO->setPostfix('Edit Test Category Offer Postfix');

        }

        self::assertTrue($CategoryProductOffersDTO->isOffer());

        self::assertTrue($CategoryProductOffersDTO->getPrice());
        $CategoryProductOffersDTO->setPrice(false);

        self::assertTrue($CategoryProductOffersDTO->getImage());
        $CategoryProductOffersDTO->setImage(true);

        self::assertFalse($CategoryProductOffersDTO->isPostfix());
        $CategoryProductOffersDTO->setPostfix(true);

        self::assertTrue($CategoryProductOffersDTO->getQuantitative());
        $CategoryProductOffersDTO->setQuantitative(true);

        self::assertTrue($CategoryProductOffersDTO->getReference()->getType() === 'input');


        /* * */


        /** @var CategoryProductVariationDTO $CategoryProductVariationDTO */
        $CategoryProductVariationDTO = $CategoryProductOffersDTO->getVariation();

        /** @var CategoryProductVariationTransDTO $CategoryProductVariationTransDTO */
        foreach($CategoryProductVariationDTO->getTranslate() as $CategoryProductVariationTransDTO)
        {
            self::assertEquals('Test Category Variation Name', $CategoryProductVariationTransDTO->getName());
            $CategoryProductVariationTransDTO->setName('Edit Test Category Variation Name');

            self::assertEquals('Test Category Variation Postfix', $CategoryProductVariationTransDTO->getPostfix());
            $CategoryProductVariationTransDTO->setPostfix('Edit Test Category Variation Postfix');

        }

        self::assertTrue($CategoryProductVariationDTO->isVariation());

        self::assertTrue($CategoryProductVariationDTO->getPrice());
        $CategoryProductVariationDTO->setPrice(true);

        self::assertTrue($CategoryProductVariationDTO->getImage());
        $CategoryProductVariationDTO->setImage(true);

        self::assertFalse($CategoryProductVariationDTO->isPostfix());
        $CategoryProductVariationDTO->setPostfix(true);

        self::assertTrue($CategoryProductVariationDTO->getQuantitative());
        $CategoryProductVariationDTO->setQuantitative(true);

        self::assertTrue($CategoryProductVariationDTO->getReference()->getType() === 'input');


        /** @var CategoryProductModificationDTO $CategoryProductModificationDTO */
        $CategoryProductModificationDTO = $CategoryProductVariationDTO->getModification();

        /** @var CategoryProductModificationTransDTO $CategoryProductModificationTransDTO */
        foreach($CategoryProductModificationDTO->getTranslate() as $CategoryProductModificationTransDTO)
        {
            self::assertEquals('Test Category Modification Name', $CategoryProductModificationTransDTO->getName());
            $CategoryProductModificationTransDTO->setName('Edit Test Category Modification Name');

            self::assertEquals('Test Category Modification Postfix', $CategoryProductModificationTransDTO->getPostfix());
            $CategoryProductModificationTransDTO->setPostfix('Edit Test Category Modification Postfix');

        }

        self::assertTrue($CategoryProductModificationDTO->isModification());

        self::assertTrue($CategoryProductModificationDTO->getPrice());
        $CategoryProductModificationDTO->setPrice(true);

        self::assertTrue($CategoryProductModificationDTO->getImage());
        $CategoryProductModificationDTO->setImage(false);

        self::assertTrue($CategoryProductModificationDTO->getPostfix());
        $CategoryProductModificationDTO->setPostfix(false);

        self::assertTrue($CategoryProductModificationDTO->getQuantitative());
        $CategoryProductModificationDTO->setQuantitative(false);

        self::assertTrue($CategoryProductModificationDTO->getReference()->getType() === 'input');


        //        /** @var CategoryProductHandler $CategoryProductHandler */
        //        $CategoryProductHandler = self::getContainer()->get(CategoryProductHandler::class);
        //        $handle = $CategoryProductHandler->handle($CategoryProductDTO);
        //
        //        self::assertTrue(($handle instanceof CategoryProduct), $handle.': Ошибка CategoryProduct');

    }

}
