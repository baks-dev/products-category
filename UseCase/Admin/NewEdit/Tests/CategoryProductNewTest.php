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
use BaksDev\Products\Category\Entity\CategoryProduct;
use BaksDev\Products\Category\Entity\Event\CategoryProductEvent;
use BaksDev\Products\Category\Type\Id\CategoryProductUid;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\CategoryProductDTO;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\CategoryProductHandler;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\Landing\ProductLandingCollectionDTO;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\Offers\CategoryProductOffersDTO;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\Offers\Trans\ProductOffersTransDTO;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\Offers\Variation\CategoryProductVariationDTO;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\Offers\Variation\Modification\CategoryProductModificationDTO;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\Offers\Variation\Modification\Trans\CategoryProductModificationTransDTO;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\Offers\Variation\Trans\CategoryProductVariationTransDTO;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\Section\Fields\ProductSectionFieldCollectionDTO;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\Section\Fields\Trans\ProductSectionFieldTransDTO;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\Section\ProductSectionCollectionDTO;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\Section\Trans\ProductSectionTransDTO;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\Seo\ProductSeoCollectionDTO;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\Trans\CategoryProductTransDTO;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;

/**
 * @group products-category
 * @group category-product-usecase
 */
#[When(env: 'test')]
class CategoryProductNewTest extends KernelTestCase
{
    public static function setUpBeforeClass(): void
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
        /** @see CategoryProductDTO */
        $CategoryProductDTO = new CategoryProductDTO();


        $CategoryProductDTO->setSort(123);
        self::assertEquals('123', $CategoryProductDTO->getSort());

        $ProductInfoDTO = $CategoryProductDTO->getInfo();


        $ProductInfoDTO->setActive(true);
        self::assertTrue($ProductInfoDTO->getActive());
        $ProductInfoDTO->setActive(false);
        self::assertFalse($ProductInfoDTO->getActive());


        $ProductInfoDTO->setUrl('test_category_url');
        self::assertEquals('test_category_url', $ProductInfoDTO->getUrl());


        $CategoryProductDTO->getLanding();

        /** @var ProductLandingCollectionDTO $ProductLandingCollectionDTO */
        foreach($CategoryProductDTO->getLanding() as $ProductLandingCollectionDTO)
        {
            $ProductLandingCollectionDTO->setHeader('Test Landing Header');
            self::assertEquals('Test Landing Header', $ProductLandingCollectionDTO->getHeader());

            $ProductLandingCollectionDTO->setBottom('Test Landing Bottom');
            self::assertEquals('Test Landing Bottom', $ProductLandingCollectionDTO->getBottom());
        }


        /** @var ProductSeoCollectionDTO $ProductSeoCollectionDTO */
        foreach($CategoryProductDTO->getSeo() as $ProductSeoCollectionDTO)
        {
            $ProductSeoCollectionDTO->setTitle('Test Category Seo Title');
            self::assertEquals('Test Category Seo Title', $ProductSeoCollectionDTO->getTitle());

            $ProductSeoCollectionDTO->setDescription('Test Category Seo Description');
            self::assertEquals('Test Category Seo Description', $ProductSeoCollectionDTO->getDescription());

            $ProductSeoCollectionDTO->setKeywords('Test Category Seo Keywords');
            self::assertEquals('Test Category Seo Keywords', $ProductSeoCollectionDTO->getKeywords());

        }


        /** @var ProductSectionCollectionDTO $ProductSectionCollectionDTO */

        $ProductSectionCollectionDTO = new ProductSectionCollectionDTO();
        $CategoryProductDTO->addSection($ProductSectionCollectionDTO);
        self::assertCount(1, $CategoryProductDTO->getSection());


        $ProductSectionFieldCollectionDTO = new ProductSectionFieldCollectionDTO();

        $ProductSectionFieldCollectionDTO->setSort(112);
        self::assertEquals(112, $ProductSectionFieldCollectionDTO->getSort());

        $ProductSectionFieldCollectionDTO->setType($InputField = new InputField('input'));
        self::assertSame($InputField, $ProductSectionFieldCollectionDTO->getType());

        $ProductSectionFieldCollectionDTO->setName(true);
        self::assertTrue($ProductSectionFieldCollectionDTO->getName());
        $ProductSectionFieldCollectionDTO->setName(false);
        self::assertFalse($ProductSectionFieldCollectionDTO->getName());

        $ProductSectionFieldCollectionDTO->setRequired(true);
        self::assertTrue($ProductSectionFieldCollectionDTO->getRequired());
        $ProductSectionFieldCollectionDTO->setRequired(false);
        self::assertFalse($ProductSectionFieldCollectionDTO->getRequired());

        $ProductSectionFieldCollectionDTO->setAlternative(true);
        self::assertTrue($ProductSectionFieldCollectionDTO->getAlternative());
        $ProductSectionFieldCollectionDTO->setAlternative(false);
        self::assertFalse($ProductSectionFieldCollectionDTO->getAlternative());

        $ProductSectionFieldCollectionDTO->setFilter(true);
        self::assertTrue($ProductSectionFieldCollectionDTO->getFilter());
        $ProductSectionFieldCollectionDTO->setFilter(false);
        self::assertFalse($ProductSectionFieldCollectionDTO->getFilter());

        $ProductSectionFieldCollectionDTO->setPhoto(true);
        self::assertTrue($ProductSectionFieldCollectionDTO->getPhoto());
        $ProductSectionFieldCollectionDTO->setPhoto(false);
        self::assertFalse($ProductSectionFieldCollectionDTO->getPhoto());

        $ProductSectionFieldCollectionDTO->setPublic(true);
        self::assertTrue($ProductSectionFieldCollectionDTO->getPublic());
        $ProductSectionFieldCollectionDTO->setPublic(false);
        self::assertFalse($ProductSectionFieldCollectionDTO->getPublic());


        /** @var ProductSectionFieldTransDTO $ProductSectionFieldTransDTO */
        foreach($ProductSectionFieldCollectionDTO->getTranslate() as $ProductSectionFieldTransDTO)
        {
            $ProductSectionFieldTransDTO->setName('Test Category Section Field Name');
            self::assertEquals('Test Category Section Field Name', $ProductSectionFieldTransDTO->getName());
            $ProductSectionFieldTransDTO->setDescription('Test Category Section Field Description');
            self::assertEquals('Test Category Section Field Description', $ProductSectionFieldTransDTO->getDescription());
        }


        $ProductSectionCollectionDTO->addField($ProductSectionFieldCollectionDTO);
        self::assertCount(1, $ProductSectionCollectionDTO->getField());

        /** @var ProductSectionTransDTO $ProductSectionTransDTO */
        foreach($ProductSectionCollectionDTO->getTranslate() as $ProductSectionTransDTO)
        {
            $ProductSectionTransDTO->setName('Test Category Section Name');
            self::assertEquals('Test Category Section Name', $ProductSectionTransDTO->getName());

            $ProductSectionTransDTO->setDescription('Test Category Section Description');
            self::assertEquals('Test Category Section Description', $ProductSectionTransDTO->getDescription());
        }


        /** @var CategoryProductTransDTO $CategoryProductTransDTO */
        foreach($CategoryProductDTO->getTranslate() as $CategoryProductTransDTO)
        {
            $CategoryProductTransDTO->setName('Test Category Name');
            self::assertEquals('Test Category Name', $CategoryProductTransDTO->getName());

            $CategoryProductTransDTO->setDescription('Test Category Description');
            self::assertEquals('Test Category Description', $CategoryProductTransDTO->getDescription());
        }


        /** @var CategoryProductOffersDTO $CategoryProductOffersDTO */
        $CategoryProductOffersDTO = $CategoryProductDTO->getOffer();

        /** @var ProductOffersTransDTO $ProductOffersTransDTO */
        foreach($CategoryProductOffersDTO->getTranslate() as $ProductOffersTransDTO)
        {
            $ProductOffersTransDTO->setName('Test Category Offer Name');
            self::assertEquals('Test Category Offer Name', $ProductOffersTransDTO->getName());

            $ProductOffersTransDTO->setPostfix('Test Category Offer Postfix');
            self::assertEquals('Test Category Offer Postfix', $ProductOffersTransDTO->getPostfix());
        }


        $CategoryProductOffersDTO->setOffer(false);
        self::assertFalse($CategoryProductOffersDTO->isOffer());
        $CategoryProductOffersDTO->setOffer(true);
        self::assertTrue($CategoryProductOffersDTO->isOffer());


        $CategoryProductOffersDTO->setPrice(true);
        self::assertTrue($CategoryProductOffersDTO->getPrice());
        $CategoryProductOffersDTO->setPrice(false);
        self::assertFalse($CategoryProductOffersDTO->getPrice());

        $CategoryProductOffersDTO->setImage(true);
        self::assertTrue($CategoryProductOffersDTO->getImage());
        $CategoryProductOffersDTO->setImage(false);
        self::assertFalse($CategoryProductOffersDTO->getImage());


        $CategoryProductOffersDTO->setPostfix(true);
        self::assertTrue($CategoryProductOffersDTO->isPostfix());
        $CategoryProductOffersDTO->setPostfix(false);
        self::assertFalse($CategoryProductOffersDTO->isPostfix());


        $CategoryProductOffersDTO->setQuantitative(true);
        self::assertTrue($CategoryProductOffersDTO->getQuantitative());
        $CategoryProductOffersDTO->setQuantitative(false);
        self::assertFalse($CategoryProductOffersDTO->getQuantitative());


        $CategoryProductOffersDTO->setReference($InputField = new InputField('input'));
        self::assertSame($InputField, $CategoryProductOffersDTO->getReference());


        /** @var CategoryProductVariationDTO $CategoryProductVariationDTO */
        $CategoryProductVariationDTO = $CategoryProductOffersDTO->getVariation();

        /** @var CategoryProductVariationTransDTO $CategoryProductVariationTransDTO */
        foreach($CategoryProductVariationDTO->getTranslate() as $CategoryProductVariationTransDTO)
        {
            $CategoryProductVariationTransDTO->setName('Test Category Variation Name');
            self::assertEquals('Test Category Variation Name', $CategoryProductVariationTransDTO->getName());

            $CategoryProductVariationTransDTO->setPostfix('Test Category Variation Postfix');
            self::assertEquals('Test Category Variation Postfix', $CategoryProductVariationTransDTO->getPostfix());
        }

        $CategoryProductVariationDTO->setVariation(false);
        self::assertFalse($CategoryProductVariationDTO->isVariation());
        $CategoryProductVariationDTO->setVariation(true);
        self::assertTrue($CategoryProductVariationDTO->isVariation());


        $CategoryProductVariationDTO->setPrice(true);
        self::assertTrue($CategoryProductVariationDTO->getPrice());
        $CategoryProductVariationDTO->setPrice(false);
        self::assertFalse($CategoryProductVariationDTO->getPrice());

        $CategoryProductVariationDTO->setImage(true);
        self::assertTrue($CategoryProductVariationDTO->getImage());
        $CategoryProductVariationDTO->setImage(false);
        self::assertFalse($CategoryProductVariationDTO->getImage());


        $CategoryProductVariationDTO->setPostfix(true);
        self::assertTrue($CategoryProductVariationDTO->isPostfix());
        $CategoryProductVariationDTO->setPostfix(false);
        self::assertFalse($CategoryProductVariationDTO->isPostfix());


        $CategoryProductVariationDTO->setQuantitative(true);
        self::assertTrue($CategoryProductVariationDTO->getQuantitative());
        $CategoryProductVariationDTO->setQuantitative(false);
        self::assertFalse($CategoryProductVariationDTO->getQuantitative());


        $CategoryProductVariationDTO->setReference($InputField = new InputField('input'));
        self::assertSame($InputField, $CategoryProductVariationDTO->getReference());


        /** @var CategoryProductModificationDTO $CategoryProductModificationDTO */
        $CategoryProductModificationDTO = $CategoryProductVariationDTO->getModification();

        /** @var CategoryProductModificationTransDTO $CategoryProductModificationTransDTO */
        foreach($CategoryProductModificationDTO->getTranslate() as $CategoryProductModificationTransDTO)
        {
            $CategoryProductModificationTransDTO->setName('Test Category Modification Name');
            self::assertEquals('Test Category Modification Name', $CategoryProductModificationTransDTO->getName());

            $CategoryProductModificationTransDTO->setPostfix('Test Category Modification Postfix');
            self::assertEquals('Test Category Modification Postfix', $CategoryProductModificationTransDTO->getPostfix());
        }


        $CategoryProductModificationDTO->setModification(false);
        self::assertFalse($CategoryProductModificationDTO->isModification());
        $CategoryProductModificationDTO->setModification(true);
        self::assertTrue($CategoryProductModificationDTO->isModification());

        $CategoryProductModificationDTO->setPrice(false);
        self::assertFalse($CategoryProductModificationDTO->getPrice());
        $CategoryProductModificationDTO->setPrice(true);
        self::assertTrue($CategoryProductModificationDTO->getPrice());

        $CategoryProductModificationDTO->setImage(false);
        self::assertFalse($CategoryProductModificationDTO->getImage());
        $CategoryProductModificationDTO->setImage(true);
        self::assertTrue($CategoryProductModificationDTO->getImage());


        $CategoryProductModificationDTO->setPostfix(false);
        self::assertFalse($CategoryProductModificationDTO->getPostfix());
        $CategoryProductModificationDTO->setPostfix(true);
        self::assertTrue($CategoryProductModificationDTO->getPostfix());


        $CategoryProductModificationDTO->setQuantitative(false);
        self::assertFalse($CategoryProductModificationDTO->getQuantitative());
        $CategoryProductModificationDTO->setQuantitative(true);
        self::assertTrue($CategoryProductModificationDTO->getQuantitative());


        $CategoryProductModificationDTO->setReference($InputField = new InputField('input'));
        self::assertSame($InputField, $CategoryProductModificationDTO->getReference());


        /** @var CategoryProductHandler $CategoryProductHandler */
        $CategoryProductHandler = self::getContainer()->get(CategoryProductHandler::class);
        $handle = $CategoryProductHandler->handle($CategoryProductDTO);

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

        self::assertTrue($dbal->fetchExist());
    }
}
