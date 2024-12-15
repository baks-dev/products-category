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

namespace BaksDev\Products\Category\Repository\MenuPublicCategory;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Products\Category\Entity\CategoryProduct;
use BaksDev\Products\Category\Entity\Cover\CategoryProductCover;
use BaksDev\Products\Category\Entity\Event\CategoryProductEvent;
use BaksDev\Products\Category\Entity\Info\CategoryProductInfo;
use BaksDev\Products\Category\Entity\Trans\CategoryProductTrans;
use BaksDev\Products\Product\Entity\Category\ProductCategory;
use BaksDev\Products\Product\Entity\Info\ProductInfo;
use BaksDev\Products\Product\Entity\Offers\Image\ProductOfferImage;
use BaksDev\Products\Product\Entity\Offers\ProductOffer;
use BaksDev\Products\Product\Entity\Offers\Quantity\ProductOfferQuantity;
use BaksDev\Products\Product\Entity\Offers\Variation\Image\ProductVariationImage;
use BaksDev\Products\Product\Entity\Offers\Variation\Modification\Image\ProductModificationImage;
use BaksDev\Products\Product\Entity\Offers\Variation\Modification\ProductModification;
use BaksDev\Products\Product\Entity\Offers\Variation\Modification\Quantity\ProductModificationQuantity;
use BaksDev\Products\Product\Entity\Offers\Variation\ProductVariation;
use BaksDev\Products\Product\Entity\Offers\Variation\Quantity\ProductVariationQuantity;
use BaksDev\Products\Product\Entity\Photo\ProductPhoto;
use BaksDev\Products\Product\Entity\Price\ProductPrice;
use BaksDev\Products\Product\Entity\Product;
use BaksDev\Products\Product\Entity\Trans\ProductTrans;


final readonly class MenuPublicCategoryRepository implements MenuPublicCategoryInterface
{
    public function __construct(private DBALQueryBuilder $DBALQueryBuilder) {}

    /**
     * Метод возвращает все категории меню
     */
    public function findAll(): array|bool
    {
        $dbal = $this->DBALQueryBuilder
            ->createQueryBuilder(self::class)
            ->bindLocal();

        /* Категория */
        $dbal
            ->select('category.id')
            ->addSelect('category.event AS event')
            ->from(CategoryProduct::class, 'category');

        /* События категории */
        $dbal
            ->addSelect('category_event.sort AS category_sort')
            ->addSelect('category_event.parent AS category_parent')
            ->join(
                'category',
                CategoryProductEvent::class,
                'category_event',
                'category_event.id = category.event AND category_event.parent IS NULL'
            );

        $dbal
            ->addSelect('category_info.url AS category_url')
            ->join(
                'category_event',
                CategoryProductInfo::class,
                'category_info',
                'category_info.event = category.event AND category_info.active = true'
            );

        /* Обложка */
        $dbal
            ->addSelect('category_cover.ext AS category_cover_ext')
            ->addSelect('category_cover.cdn AS category_cover_cdn')
            ->addSelect(
                "
			CASE
			 WHEN category_cover.name IS NOT NULL THEN
					CONCAT ( '/upload/".$dbal->table(CategoryProductCover::class)."' , '/', category_cover.name)
			   		ELSE NULL
			END AS category_cover_dir
		"
            );

        $dbal
            ->leftJoin(
                'category_event',
                CategoryProductCover::class,
                'category_cover',
                'category_cover.event = category_event.id'
            );

        /* Перевод категории */
        $dbal
            ->addSelect('category_trans.name AS category_name')
            ->addSelect('category_trans.description AS category_description')
            ->leftJoin(
                'category_event',
                CategoryProductTrans::class,
                'category_trans',
                'category_trans.event = category_event.id AND category_trans.local = :local'
            );


        /* Продукция корневой категории */

        $dbal->leftJoin(
            'category',
            ProductCategory::class,
            'product_category',
            'product_category.category = category.id AND product_category.root = true'
        );


        $dbal->leftJoin(
            'product_category',
            Product::class,
            'product',
            'product.event = product_category.event'
        );

        $dbal->leftJoin(
            'product_category',
            ProductPrice::class,
            'product_price',
            'product_price.event = product.event'
        );

        $dbal
            ->leftJoin(
                'product',
                ProductInfo::class,
                'product_info',
                'product_info.product = product.id'
            );

        $dbal
            ->leftJoin(
                'product',
                ProductTrans::class,
                'product_trans',
                'product_trans.event = product.event AND product_trans.local = :local'
            );


        $dbal->leftJoin(
            'product',
            ProductOffer::class,
            'product_offer',
            'product_offer.event = product.event'
        );

        $dbal->leftJoin(
            'product_offer',
            ProductOfferQuantity::class,
            'product_offer_quantity',
            'product_offer_quantity.offer = product_offer.id'
        );


        $dbal->leftJoin(
            'product_offer',
            ProductVariation::class,
            'product_variation',
            'product_variation.offer = product_offer.id'
        );

        $dbal->leftJoin(
            'product_variation',
            ProductVariationQuantity::class,
            'product_variation_quantity',
            'product_variation_quantity.variation = product_variation.id'
        );


        $dbal->leftJoin(
            'product_variation',
            ProductModification::class,
            'product_modification',
            'product_modification.variation = product_variation.id'
        );


        $dbal->leftJoin(
            'product_modification',
            ProductModificationQuantity::class,
            'product_modification_quantity',
            'product_modification_quantity.modification = product_modification.id'
        );


        // Фото продукта

        $dbal->leftJoin(
            'product_modification',
            ProductModificationImage::class,
            'product_modification_image',
            '
                product_modification_image.modification = product_modification.id AND
                product_modification_image.root = true
			'
        );

        $dbal->leftJoin(
            'product_offer',
            ProductVariationImage::class,
            'product_variation_image',
            '
                product_variation_image.variation = product_variation.id AND
                product_variation_image.root = true
			'
        );

        $dbal->leftJoin(
            'product_offer',
            ProductOfferImage::class,
            'product_offer_images',
            '
			product_variation_image.name IS NULL AND
			product_offer_images.offer = product_offer.id AND
			product_offer_images.root = true
			'
        );

        $dbal->leftJoin(
            'product_offer',
            ProductPhoto::class,
            'product_photo',
            '
                product_offer_images.name IS NULL AND
                product_photo.event = product.event AND
                product_photo.root = true
			'
        );


        $dbal->addSelect(
            "JSON_AGG
            ( DISTINCT
                
                    JSONB_BUILD_OBJECT
                    (
                        '0', COALESCE(
                               product_modification_quantity.reserve, 
                               product_variation_quantity.reserve, 
                               product_offer_quantity.reserve, 
                               product_price.reserve
                            ),
                            
                        'category', product_category.category,
                        'url', product_info.url,
      
                        'category_url', category_info.url,
                        'name', product_trans.name,
                        
                        'is_offer', CASE WHEN product_offer.id IS NOT NULL THEN TRUE ELSE FALSE END,
                
                         
                         'price', CASE 
                             WHEN product_offer.id IS NOT NULL 
                             THEN (SELECT 
                              
                              MIN(COALESCE(
                                    product_modification_price.price, 
                                    product_variation_price.price, 
                                    product_offer_price.price, 
                                    product_price.price
                              )) as price
                              
                              FROM product_offer  
                              LEFT JOIN product_offer_price ON product_offer_price.offer = product_offer.id
                              LEFT JOIN product_offer_quantity ON product_offer_quantity.offer = product_offer.id
   
                              LEFT JOIN product_variation ON product_variation.offer = product_offer.id
                              LEFT JOIN product_variation_price ON product_variation_price.variation = product_variation.id
                              LEFT JOIN product_variation_quantity ON product_variation_quantity.variation = product_variation.id
                              
                              LEFT JOIN product_modification ON product_modification.variation = product_variation.id
                              LEFT JOIN product_modification_price ON product_modification_price.modification = product_modification.id
                              LEFT JOIN product_modification_quantity ON product_modification_quantity.modification = product_modification.id
                             
                              LEFT JOIN product_price ON product_price.event = product_offer.event

                              WHERE product_offer.event = product.event

                              AND COALESCE(
                                (product_modification_quantity.quantity - product_modification_quantity.reserve), 
                                (product_variation_quantity.quantity - product_variation_quantity.reserve), 
                                (product_offer_quantity.quantity - product_offer_quantity.reserve), 
                                (product_price.quantity - product_price.reserve)
                            ) > 0
                              
                              
                              AND COALESCE(
                                    product_modification_price.price, 
                                    product_variation_price.price, 
                                    product_offer_price.price, 
                                    product_price.price
                              ) > 0
                            
                         ) ELSE 0 END,
                         
           
                        'image', CASE
                                 WHEN product_modification_image.name IS NOT NULL THEN
                                        CONCAT ( '/upload/".$dbal->table(ProductModificationImage::class)."' , '/', product_modification_image.name)
                                   WHEN product_variation_image.name IS NOT NULL THEN
                                        CONCAT ( '/upload/".$dbal->table(ProductVariationImage::class)."' , '/', product_variation_image.name)
                                   WHEN product_offer_images.name IS NOT NULL THEN
                                        CONCAT ( '/upload/".$dbal->table(ProductOfferImage::class)."' , '/', product_offer_images.name)
                                   WHEN product_photo.name IS NOT NULL THEN
                                        CONCAT ( '/upload/".$dbal->table(ProductPhoto::class)."' , '/', product_photo.name)
                                   ELSE NULL
                                END,
                                
                        'ext', CASE
                           WHEN product_modification_image.name IS NOT NULL THEN  product_modification_image.ext
                           WHEN product_variation_image.name IS NOT NULL THEN product_variation_image.ext
                           WHEN product_offer_images.name IS NOT NULL THEN product_offer_images.ext
                           WHEN product_photo.name IS NOT NULL THEN product_photo.ext
                           ELSE NULL
                        END,
                        
                        'cdn', CASE
                           WHEN product_variation_image.name IS NOT NULL THEN
                                product_variation_image.cdn
                           WHEN product_offer_images.name IS NOT NULL THEN
                                product_offer_images.cdn
                           WHEN product_photo.name IS NOT NULL THEN
                                product_photo.cdn
                           ELSE NULL
                        END        
                        
                        
                    ) 
                    
                    
                    
            ) FILTER (WHERE product_info.url IS NOT NULL AND 
                COALESCE(
                    (product_modification_quantity.quantity - product_modification_quantity.reserve), 
                    (product_variation_quantity.quantity - product_variation_quantity.reserve), 
                    (product_offer_quantity.quantity - product_offer_quantity.reserve), 
                    (product_price.quantity - product_price.reserve)
                ) > 0
            ) 
			AS products"
        );


        /*







        */

        /* РАЗДЕЛЫ: 2-я вложенность  */

        // $dbal->addSelect('parent_category_event.id AS parent_event');
        $dbal->leftJoin(
            'category',
            CategoryProductEvent::class,
            'parent_category_event',
            'parent_category_event.parent = category.id'
        );

        $dbal->leftJoin(
            'parent_category_event',
            CategoryProductInfo::class,
            'parent_category_info',
            'parent_category_info.event = parent_category_event.id'
        );

        $dbal->leftJoin(
            'parent_category_event',
            CategoryProductCover::class,
            'parent_category_cover',
            'parent_category_cover.event = parent_category_event.id'
        );

        // $dbal->addSelect('parent_category_trans.name AS parent_category_name');
        $dbal->leftJoin(
            'parent_category_event',
            CategoryProductTrans::class,
            'parent_category_trans',
            'parent_category_trans.event = parent_category_event.id  AND parent_category_trans.local = :local'
        );

        // продукция вложенной категории

        $dbal->leftJoin(
            'parent_category_event',
            ProductCategory::class,
            'product_category_two',
            'product_category_two.category = parent_category_event.category AND product_category_two.root = true'
        );


        $dbal->leftJoin(
            'product_category_two',
            Product::class,
            'product_two',
            'product_two.event = product_category_two.event'
        );

        $dbal->leftJoin(
            'product_two',
            ProductPrice::class,
            'product_two_price',
            'product_two_price.event = product_two.event'
        );


        $dbal
            ->leftJoin(
                'product_two',
                ProductInfo::class,
                'product_info_two',
                'product_info_two.product = product_two.id'
            );

        $dbal
            ->leftJoin(
                'product_two',
                ProductTrans::class,
                'product_trans_two',
                'product_trans_two.event = product_two.event AND product_trans_two.local = :local'
            );


        $dbal->leftJoin(
            'product_two',
            ProductOffer::class,
            'product_offer_two',
            'product_offer_two.event = product_two.event'
        );

        $dbal->leftJoin(
            'product_offer_two',
            ProductOfferQuantity::class,
            'product_offer_two_quantity',
            'product_offer_two_quantity.offer = product_offer_two.id'
        );


        $dbal->leftJoin(
            'product_offer_two',
            ProductVariation::class,
            'product_variation_two',
            'product_variation_two.offer = product_offer_two.id'
        );

        $dbal->leftJoin(
            'product_variation_two',
            ProductVariationQuantity::class,
            'product_variation_two_quantity',
            'product_variation_two_quantity.variation = product_variation_two.id'
        );


        $dbal->leftJoin(
            'product_variation_two',
            ProductModification::class,
            'product_modification_two',
            'product_modification_two.variation = product_variation_two.id'
        );

        $dbal->leftJoin(
            'product_modification_two',
            ProductModificationQuantity::class,
            'product_modification_two_quantity',
            'product_modification_two_quantity.modification = product_modification_two.id'
        );


        // Фото продукта

        $dbal->leftJoin(
            'product_modification_two',
            ProductModificationImage::class,
            'product_modification_image_two',
            '
                product_modification_image_two.modification = product_modification_two.id AND
                product_modification_image_two.root = true
			'
        );

        $dbal->leftJoin(
            'product_offer_two',
            ProductVariationImage::class,
            'product_variation_image_two',
            '
                product_variation_image_two.variation = product_variation_two.id AND
                product_variation_image_two.root = true
			'
        );


        $dbal->leftJoin(
            'product_offer_two',
            ProductOfferImage::class,
            'product_offer_images_two',
            '
                product_variation_image_two.name IS NULL AND
                product_offer_images_two.offer = product_offer_two.id AND
                product_offer_images_two.root = true
			'
        );

        $dbal->leftJoin(
            'product_offer_two',
            ProductPhoto::class,
            'product_photo_two',
            '
                product_offer_images_two.name IS NULL AND
                product_photo_two.event = product_two.event AND
                product_photo_two.root = true
			'
        );


        $dbal->addSelect(
            "JSON_AGG
            ( DISTINCT
                
                    JSONB_BUILD_OBJECT
                    (
                        '0', parent_category_event.sort,
                        
                        'category_url', parent_category_info.url,
                        
                        'child_category_cover_name', 
                        
                        CASE 
                            WHEN parent_category_cover.name IS NOT NULL 
                            THEN CONCAT ( '/upload/".$dbal->table(CategoryProductCover::class)."' , '/', parent_category_cover.name)
                            ELSE NULL
                        END,
                        
                        'child_category_cover_ext', parent_category_cover.ext,
                        'child_category_cover_cdn', parent_category_cover.cdn,
            
                        'child_category_event', parent_category_event.id,
                        'child_category_name', parent_category_trans.name,
                        'child_category_description', parent_category_trans.description
                    )
            ) FILTER (WHERE parent_category_info.url IS NOT NULL) 
			AS child_category"
        );


        $dbal->addSelect(
            "JSON_AGG
            ( DISTINCT
                
                    JSONB_BUILD_OBJECT
                    (
                        '0', product_info_two.sort,
                        'category', parent_category_event.id,
                        'name', product_trans_two.name,
                        
                        
                        'category_url', parent_category_info.url,
                        'url', product_info_two.url,
                        
                        'is_offer', CASE WHEN product_offer_two.id IS NOT NULL THEN TRUE ELSE FALSE END,
 
                        'price', CASE 
                             WHEN product_offer_two.id IS NOT NULL 
                             THEN (SELECT 
                              
                              MIN(COALESCE(
                                    product_modification_price.price, 
                                    product_variation_price.price, 
                                    product_offer_price.price, 
                                    product_price.price
                              )) as price
                              
                              FROM product_offer 

                              LEFT JOIN product_price ON product_price.event = product_offer.event
                              LEFT JOIN product_offer_price ON product_offer_price.offer = product_offer.id
                              LEFT JOIN product_variation_price ON product_variation_price.variation = product_variation.id
                              LEFT JOIN product_modification_price ON product_modification_price.modification = product_modification.id
                              
                              WHERE product_offer.event = product_two.event AND COALESCE(
                                    product_modification_price.price, 
                                    product_variation_price.price, 
                                    product_offer_price.price, 
                                    product_price.price
                              ) > 0
                            
                         ) ELSE 0 END,
                            
                            
                       
                        'image', CASE
                                 WHEN product_modification_image_two.name IS NOT NULL THEN
                                        CONCAT ( '/upload/".$dbal->table(ProductModificationImage::class)."' , '/', product_modification_image_two.name)
                                   WHEN product_variation_image_two.name IS NOT NULL THEN
                                        CONCAT ( '/upload/".$dbal->table(ProductVariationImage::class)."' , '/', product_variation_image_two.name)
                                   WHEN product_offer_images_two.name IS NOT NULL THEN
                                        CONCAT ( '/upload/".$dbal->table(ProductOfferImage::class)."' , '/', product_offer_images_two.name)
                                   WHEN product_photo_two.name IS NOT NULL THEN
                                        CONCAT ( '/upload/".$dbal->table(ProductPhoto::class)."' , '/', product_photo_two.name)
                                   ELSE NULL
                                END,
                                
                        'ext', CASE
                           WHEN product_modification_image_two.name IS NOT NULL THEN  product_modification_image_two.ext
                           WHEN product_variation_image_two.name IS NOT NULL THEN product_variation_image_two.ext
                           WHEN product_offer_images_two.name IS NOT NULL THEN product_offer_images_two.ext
                           WHEN product_photo_two.name IS NOT NULL THEN product_photo_two.ext
                           ELSE NULL
                        END,
                        
                        'cdn', CASE
                           WHEN product_variation_image_two.name IS NOT NULL THEN
                                product_variation_image_two.cdn
                           WHEN product_offer_images_two.name IS NOT NULL THEN
                                product_offer_images_two.cdn
                           WHEN product_photo_two.name IS NOT NULL THEN
                                product_photo_two.cdn
                           ELSE NULL
                        END 

                    ) 
            ) FILTER (WHERE product_info_two.url IS NOT NULL AND 
                COALESCE(
                    (product_modification_two_quantity.quantity - product_modification_two_quantity.reserve), 
                    (product_variation_two_quantity.quantity - product_variation_two_quantity.reserve), 
                    (product_offer_two_quantity.quantity - product_offer_two_quantity.reserve), 
                    (product_two_price.quantity - product_two_price.reserve)
                ) > 0
            ) 
			AS child_products"
        );


        /*


        */

        /* РАЗДЕЛЫ: 3-я вложенность  */

        // $dbal->addSelect('parent_category_event.id AS parent_event');
        $dbal->leftJoin(
            'parent_category_event',
            CategoryProductEvent::class,
            'parent_category_event_three',
            'parent_category_event_three.parent = parent_category_event.category'
        );

        $dbal->leftJoin(
            'parent_category_event_three',
            CategoryProductInfo::class,
            'parent_category_info_three',
            'parent_category_info_three.event = parent_category_event_three.id'
        );

        $dbal->leftJoin(
            'parent_category_event_three',
            CategoryProductCover::class,
            'parent_category_cover_three',
            'parent_category_cover_three.event = parent_category_event_three.id'
        );


        $dbal->leftJoin(
            'parent_category_event_three',
            CategoryProductTrans::class,
            'parent_category_trans_three',
            'parent_category_trans_three.event = parent_category_event_three.id  AND parent_category_trans_three.local = :local'
        );


        $dbal->leftJoin(
            'parent_category_event_three',
            ProductCategory::class,
            'product_category_three',
            'product_category_three.category = parent_category_event_three.category AND product_category_three.root = true'
        );


        $dbal->leftJoin(
            'product_category_three',
            Product::class,
            'product_three',
            'product_three.event = product_category_three.event'
        );

        $dbal->leftJoin(
            'product_three',
            ProductPrice::class,
            'product_three_price',
            'product_three_price.event = product_three.event'
        );


        $dbal->leftJoin(
            'product_three',
            ProductOffer::class,
            'product_offer_three',
            'product_offer_three.event = product_three.event'
        );

        $dbal->leftJoin(
            'product_offer_three',
            ProductOfferQuantity::class,
            'product_offer_three_quantity',
            'product_offer_three_quantity.offer = product_offer_three.id'
        );


        $dbal->leftJoin(
            'product_offer_three',
            ProductVariation::class,
            'product_variation_three',
            'product_variation_three.offer = product_offer_three.id'
        );

        $dbal->leftJoin(
            'product_variation_three',
            ProductVariationQuantity::class,
            'product_variation_three_quantity',
            'product_variation_three_quantity.variation = product_variation_three.id'
        );


        $dbal->leftJoin(
            'product_variation_three',
            ProductModification::class,
            'product_modification_three',
            'product_modification_three.variation = product_variation_three.id'
        );

        $dbal->leftJoin(
            'product_modification_three',
            ProductModificationQuantity::class,
            'product_modification_three_quantity',
            'product_modification_three_quantity.modification = product_modification_three.id'
        );


        $dbal->addSelect(
            "JSON_AGG
		( DISTINCT
			
				JSONB_BUILD_OBJECT
				(
					'0', parent_category_event_three.sort,
					
					'child_category_url', parent_category_info_three.url,
					
					'url', parent_category_info_three.url,
					'name', parent_category_trans_three.name,

					'child_category_cover_name', 
					
					CASE 
					    WHEN parent_category_cover_three.name IS NOT NULL 
					    THEN CONCAT ( '/upload/".$dbal->table(CategoryProductCover::class)."' , '/', parent_category_cover_three.name)
					    ELSE NULL
					END,
					
					'child_category_cover_ext', parent_category_cover_three.ext,
					'child_category_cover_cdn', parent_category_cover_three.cdn,
		
					'child_category_event', parent_category_event_three.id,
					'category', parent_category_event.id,
					'child_category_name', parent_category_trans_three.name,
					
					'child_category_description', parent_category_trans_three.description

				)
		) FILTER (WHERE parent_category_info_three.url IS NOT NULL AND product_three.id IS NOT NULL 
		
		
		 AND 
                COALESCE(
                    (product_modification_three_quantity.quantity - product_modification_three_quantity.reserve), 
                    (product_variation_three_quantity.quantity - product_variation_three_quantity.reserve), 
                    (product_offer_three_quantity.quantity - product_offer_three_quantity.reserve), 
                    (product_three_price.quantity - product_three_price.reserve)
                ) > 0
		
		) 
	
			AS child_category_three"
        );

        $dbal->orderBy('category_event.sort', 'ASC');

        $dbal->allGroupByExclude();

        //dump($dbal->fetchAllAssociativeIndexed());

        return $dbal
            ->enableCache('products-product', 86400)
            ->fetchAllAssociativeIndexed();

    }

}