<?php
/*
 *  Copyright 2025.  Baks.dev <admin@baks.dev>
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

namespace BaksDev\Products\Category\Repository\MenuPublicCategory;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Products\Category\Entity\CategoryProduct;
use BaksDev\Products\Category\Entity\Cover\CategoryProductCover;
use BaksDev\Products\Category\Entity\Event\CategoryProductEvent;
use BaksDev\Products\Category\Entity\Info\CategoryProductInfo;
use BaksDev\Products\Category\Entity\Trans\CategoryProductTrans;
use BaksDev\Products\Category\Repository\MenuPublicCategory\MenuPublicCategoryResult\MenuPublicCategoryDTO;
use BaksDev\Products\Category\Repository\MenuPublicCategory\MenuPublicCategoryResult\MenuPublicCategoryResult;
use BaksDev\Products\Category\Repository\MenuPublicCategory\MenuPublicCategoryResult\MenuPublicProductDTO;
use BaksDev\Products\Product\Entity\Category\ProductCategory;
use BaksDev\Products\Product\Entity\Info\ProductInfo;
use BaksDev\Products\Product\Entity\Offers\Image\ProductOfferImage;
use BaksDev\Products\Product\Entity\Offers\Price\ProductOfferPrice;
use BaksDev\Products\Product\Entity\Offers\ProductOffer;
use BaksDev\Products\Product\Entity\Offers\Quantity\ProductOfferQuantity;
use BaksDev\Products\Product\Entity\Offers\Variation\Image\ProductVariationImage;
use BaksDev\Products\Product\Entity\Offers\Variation\Modification\Image\ProductModificationImage;
use BaksDev\Products\Product\Entity\Offers\Variation\Modification\Price\ProductModificationPrice;
use BaksDev\Products\Product\Entity\Offers\Variation\Modification\ProductModification;
use BaksDev\Products\Product\Entity\Offers\Variation\Modification\Quantity\ProductModificationQuantity;
use BaksDev\Products\Product\Entity\Offers\Variation\Price\ProductVariationPrice;
use BaksDev\Products\Product\Entity\Offers\Variation\ProductVariation;
use BaksDev\Products\Product\Entity\Offers\Variation\Quantity\ProductVariationQuantity;
use BaksDev\Products\Product\Entity\Photo\ProductPhoto;
use BaksDev\Products\Product\Entity\Price\ProductPrice;
use BaksDev\Products\Product\Entity\Product;
use BaksDev\Products\Product\Entity\Trans\ProductTrans;
use BaksDev\Users\Profile\UserProfile\Entity\Info\UserProfileInfo;
use BaksDev\Users\Profile\UserProfile\Repository\UserProfileTokenStorage\UserProfileTokenStorageInterface;
use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;
use BaksDev\Users\Profile\UserProfile\Type\UserProfileStatus\Status\UserProfileStatusActive;
use BaksDev\Users\Profile\UserProfile\Type\UserProfileStatus\UserProfileStatus;
use BaksDev\Wildberries\Orders\Type\DeliveryType\TypeDeliveryDbsWildberries;
use BaksDev\Wildberries\Orders\Type\DeliveryType\TypeDeliveryFbsWildberries;
use Doctrine\DBAL\ArrayParameterType;


final readonly class MenuPublicCategoryRepository implements MenuPublicCategoryInterface
{
    public function __construct(
        private DBALQueryBuilder $DBALQueryBuilder,
        private UserProfileTokenStorageInterface $UserProfileTokenStorageInterface,
    ) {}

    /**
     * Метод возвращает все категории меню
     */
    public function findAll(bool $products = false): MenuPublicCategoryResult|bool
    {

        $dbal = $this->DBALQueryBuilder
            ->createQueryBuilder(self::class)
            ->bindLocal();

        // Категория
        $dbal
            ->select('category.id')
            ->addSelect('category.event')
            ->from(CategoryProduct::class, 'category');

        $dbal
            ->addSelect('category_event.sort')
            ->addSelect('category_event.parent')
            ->joinRecursive(
                'category',
                CategoryProductEvent::class,
                'category_event',
                'category_event.id = category.event',
            );


        $dbal
            ->addSelect('category_info.url AS category_url')
            ->join(
                'category_event',
                CategoryProductInfo::class,
                'category_info',
                '
                    category_info.event = category.event AND 
                    category_info.active IS TRUE',
            );

        $dbal
            ->addSelect('category_trans.name AS category_name')
            ->leftJoin(
                'category',
                CategoryProductTrans::class,
                'category_trans',
                'category_trans.event = category.event AND category_trans.local = :local',
            );


        $dbal
            ->addSelect("CONCAT ('/upload/".$dbal->table(CategoryProductCover::class)."' , '/', category_cover.name) AS category_cover_image")
            ->addSelect('category_cover.cdn AS category_cover_cdn')
            ->addSelect('category_cover.ext AS category_cover_ext')
            ->leftJoin(
                'category',
                CategoryProductCover::class,
                'category_cover',
                'category_cover.event = category.event',
            );


        $result = $dbal
            ->enableCache('products-category')
            ->findAllRecursive(['parent' => 'id']);


        /**
         * Добавляем категории в результат
         */

        $MenuPublicCategoryResult = new MenuPublicCategoryResult($result);

        /** Если не требуется продукция по категориям */
        if(false === $products)
        {
            return $MenuPublicCategoryResult;
        }

        /**
         * Получаем продукцию по категориям
         */

        /** @var MenuPublicCategoryDTO $category */
        foreach($MenuPublicCategoryResult as $category)
        {

            $dbal = $this->DBALQueryBuilder
                ->createQueryBuilder(self::class)
                ->bindLocal();

            $dbal->from(
                ProductCategory::class,
                'product_category',
            )
                ->where('product_category.category IN (:category)')
                ->setParameter(
                    key: 'category',
                    value: $category->getAllCategoryIdentifier(),
                    type: ArrayParameterType::STRING,
                );


            $dbal->leftJoin(
                'product_category',
                Product::class,
                'product',
                'product.event = product_category.event',
            );

            $dbal
                ->addSelect('product_info.url')
                ->leftJoin(
                    'product',
                    ProductInfo::class,
                    'product_info',
                    'product_info.product = product.id',
                );

            $dbal
                ->addSelect('product_trans.name')
                ->leftJoin(
                    'product',
                    ProductTrans::class,
                    'product_trans',
                    'product_trans.event = product.event AND product_trans.local = :local',
                );


            /**
             * Торговые предложения
             */

            $dbal->leftJoin(
                'product',
                ProductOffer::class,
                'product_offer',
                'product_offer.event = product.event',
            );

            $dbal->leftJoin(
                'product_offer',
                ProductVariation::class,
                'product_variation',
                'product_variation.offer = product_offer.id',
            );

            $dbal->leftJoin(
                'product_variation',
                ProductModification::class,
                'product_modification',
                'product_modification.variation = product_variation.id',
            );


            /**
             * Количественный учет
             */

            $dbal->leftJoin(
                'product_offer',
                ProductOfferQuantity::class,
                'product_offer_quantity',
                'product_offer_quantity.offer = product_offer.id',
            );


            $dbal->leftJoin(
                'product_variation',
                ProductVariationQuantity::class,
                'product_variation_quantity',
                'product_variation_quantity.variation = product_variation.id',
            );

            $dbal->leftJoin(
                'product_modification',
                ProductModificationQuantity::class,
                'product_modification_quantity',
                'product_modification_quantity.modification = product_modification.id',
            );


            /**
             * Стоимость продукции
             */

            $dbal
                ->leftJoin(
                    'product',
                    ProductPrice::class,
                    'product_price',
                    'product_price.event = product.event',
                );

            $dbal->leftJoin(
                'product_offer',
                ProductOfferPrice::class,
                'product_offer_price',
                'product_offer_price.offer = product_offer.id',
            );

            $dbal->leftJoin(
                'product_variation',
                ProductVariationPrice::class,
                'product_variation_price',
                'product_variation_price.variation = product_variation.id',
            );

            $dbal->leftJoin(
                'product_modification',
                ProductModificationPrice::class,
                'product_modification_price',
                'product_modification_price.modification = product_modification.id',
            );


            // Фото продукта

            $dbal->leftJoin(
                'product_modification',
                ProductModificationImage::class,
                'product_modification_image',
                '
                    product_modification_image.modification = product_modification.id AND
                    product_modification_image.root = true
			');

            $dbal->leftJoin(
                'product_offer',
                ProductVariationImage::class,
                'product_variation_image',
                '
                    product_variation_image.variation = product_variation.id AND
                    product_variation_image.root = true
			');

            $dbal->leftJoin(
                'product_offer',
                ProductOfferImage::class,
                'product_offer_images',
                '
                product_variation_image.name IS NULL AND
                product_offer_images.offer = product_offer.id AND
                product_offer_images.root = true
			');

            $dbal->leftJoin(
                'product_offer',
                ProductPhoto::class,
                'product_photo',
                '
                product_offer_images.name IS NULL AND
                product_photo.event = product.event AND
                product_photo.root = true
			');

            $dbal->addSelect("
                CASE
    
                    WHEN product_modification_image.name IS NOT NULL THEN
                        CONCAT ( '/upload/".$dbal->table(ProductModificationImage::class)."' , '/', product_modification_image.name)
                    WHEN product_variation_image.name IS NOT NULL THEN
                        CONCAT ( '/upload/".$dbal->table(ProductVariationImage::class)."' , '/', product_variation_image.name)
                    WHEN product_offer_images.name IS NOT NULL THEN
                        CONCAT ( '/upload/".$dbal->table(ProductOfferImage::class)."' , '/', product_offer_images.name)
                    WHEN product_photo.name IS NOT NULL THEN
                        CONCAT ( '/upload/".$dbal->table(ProductPhoto::class)."' , '/', product_photo.name)
                    ELSE NULL
                   
                END AS product_image
            ");


            // Расширение файла
            $dbal->addSelect(
                '
            COALESCE(
                product_modification_image.ext,
                product_variation_image.ext,
                product_offer_images.ext,
                product_photo.ext
            ) AS product_image_ext',
            );


            $dbal->addSelect(
                '
            COALESCE(
                product_modification_image.cdn,
                product_variation_image.cdn,
                product_offer_images.cdn,
                product_photo.cdn
            ) AS product_image_cdn',
            );


            /** Персональная скидка из профиля авторизованного пользователя */
            if(true === $this->UserProfileTokenStorageInterface->isUser())
            {
                $profile = $this->UserProfileTokenStorageInterface->getProfileCurrent();

                if($profile instanceof UserProfileUid)
                {
                    $dbal
                        ->addSelect('profile_info.discount AS profile_discount')
                        ->leftJoin(
                            'product',
                            UserProfileInfo::class,
                            'profile_info',
                            '
                        profile_info.profile = :profile AND 
                        profile_info.status = :profile_status',
                        )
                        ->setParameter(
                            key: 'profile',
                            value: $profile,
                            type: UserProfileUid::TYPE)
                        /** Активный статус профиля */
                        ->setParameter(
                            key: 'profile_status',
                            value: UserProfileStatusActive::class,
                            type: UserProfileStatus::TYPE,
                        );
                }

            }

            $dbal->addSelect('
                MIN(COALESCE(
                    product_modification_price.price, 
                    product_variation_price.price, 
                    product_offer_price.price, 
                    product_price.price
              )) as min_price
            ');


            $dbal->addSelect('
                COALESCE(
                    product_modification_price.currency, 
                    product_variation_price.currency, 
                    product_offer_price.currency, 
                    product_price.currency
              ) as product_currency
            ');


            /** Доступное количество с учетом резерва */
            $dbal->addSelect('
                COALESCE(
                   (product_modification_quantity.quantity - product_modification_quantity.reserve),
                   (product_variation_quantity.quantity - product_variation_quantity.reserve),
                   (product_offer_quantity.quantity - product_offer_quantity.reserve),
                   (product_price.quantity - product_price.reserve)
                ) AS total
            ');

            /** Сортируем список по количеству резерва продукции, суммируем если группировка по иному свойству */
            $dbal->addOrderBy('SUM(product_modification_quantity.reserve)', 'DESC');
            $dbal->addOrderBy('SUM(product_variation_quantity.reserve)', 'DESC');
            $dbal->addOrderBy('SUM(product_offer_quantity.reserve)', 'DESC');
            $dbal->addOrderBy('SUM(product_price.reserve)', 'DESC');

            $dbal->addOrderBy('SUM(product_modification_quantity.quantity)', 'DESC');
            $dbal->addOrderBy('SUM(product_variation_quantity.quantity)', 'DESC');
            $dbal->addOrderBy('SUM(product_offer_quantity.quantity)', 'DESC');
            $dbal->addOrderBy('SUM(product_price.quantity)', 'DESC');


            /** Только в наличии */
            $dbal->andWhere('
                COALESCE(
                    (product_modification_quantity.quantity - product_modification_quantity.reserve), 
                    (product_variation_quantity.quantity - product_variation_quantity.reserve), 
                    (product_offer_quantity.quantity - product_offer_quantity.reserve), 
                    (product_price.quantity - product_price.reserve)
                ) > 0');

            $dbal->setMaxResults(9); // доступное количество

            $dbal->allGroupByExclude();

            $category->setProducts($dbal
                ->enableCache('products-product')
                ->fetchAllHydrate(MenuPublicProductDTO::class),
            );
        }


        return $MenuPublicCategoryResult;

        /**
         * @example Пример обработки
         * @var MenuPublicCategoryDTO $item
         */
        //        foreach($MenuPublicCategoryResult as $item)
        //        {
        //            if(false === $item->isExistsProduct())
        //            {
        //                continue;
        //            }
        //
        //            dump($item->getLevel().' - '.$item->getCategoryName());
        //
        //            foreach($item->getProducts() as $product)
        //            {
        //                dump($product->getName());
        //            }
        //
        //        }

    }

}